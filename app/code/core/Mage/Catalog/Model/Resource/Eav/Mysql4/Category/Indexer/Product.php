<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Indexer_Product extends Mage_Index_Model_Mysql4_Abstract
{
    protected $_categoryTable;
    protected $_categoryProductTable;
    protected $_productWebsiteTable;
    protected $_storeTable;
    protected $_groupTable;

    protected function _construct()
    {
        $this->_init('catalog/category_product_index', 'category_id');
        $this->_categoryTable = $this->getTable('catalog/category');
        $this->_categoryProductTable = $this->getTable('catalog/category_product');
        $this->_productWebsiteTable = $this->getTable('catalog/product_website');
        $this->_storeTable = $this->getTable('core/store');
        $this->_groupTable = $this->getTable('core/store_group');
    }

    /**
     * Process product save.
     * Method is responsible for index support
     * when product was saved and assigned categories was changed.
     *
     * @param   Mage_Index_Model_Event $event
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Indexer_Product
     */
    public function catalogProductSave(Mage_Index_Model_Event $event)
    {
        $productId = $event->getEntityPk();
        $data = $event->getNewData();

        /**
         * Check if category ids were updated
         */
        if (!isset($data['category_ids'])) {
            return $this;
        }

        /**
         * Select relations to categories
         */
        $select = $this->_getWriteAdapter()->select()
            ->from(array('cp' => $this->_categoryProductTable), 'category_id')
            ->joinInner(array('ce' => $this->_categoryTable), 'ce.entity_id=cp.category_id', 'path')
            ->where('cp.product_id=?', $productId);

        /**
         * Get information about product categories
         */
        $categories = $this->_getWriteAdapter()->fetchPairs($select);
        $categoryIds = array();
        $allCategoryIds = array();

        foreach ($categories as $id=>$path) {
            $categoryIds[]  = $id;
            $allCategoryIds = array_merge($allCategoryIds, explode('/', $path));
        }
        $allCategoryIds = array_unique($allCategoryIds);
        $allCategoryIds = array_diff($allCategoryIds, $categoryIds);

        /**
         * Delete previous index data
         */
        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            $this->_getWriteAdapter()->quoteInto('product_id=?', $productId)
        );

        $this->_refreshDirectRelations($categoryIds, $productId);
        $this->_refreshAnchorRelations($allCategoryIds, $productId);
        return $this;
    }

    /**
     * Process category index after category save
     *
     * @param Mage_Index_Model_Event $event
     */
    public function catalogCategorySave(Mage_Index_Model_Event $event)
    {
        $categoryId = $event->getEntityPk();
        $select = $this->_getWriteAdapter()->select()
            ->from($this->_categoryTable, 'path')
            ->where('entity_id=?', $categoryId);
        $path = $this->_getWriteAdapter()->fetchOne($select);
        $categoryIds = explode('/', $path);

        $this->_getWriteAdapter()->delete(
            $this->getMainTable(),
            $this->_getWriteAdapter()->quoteInto('category_id IN(?)', $categoryIds)
        );
        $categoryIds = array_diff($categoryIds, array($categoryId));
        $this->_refreshDirectRelations($categoryId);
        $this->_refreshAnchorRelations($categoryIds);
    }

    /**
     * Rebuild index for direct associations categories and products
     *
     * @param   null|array $categoryIds
     * @param   null|array $productIds
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Indexer_Product
     */
    protected function _refreshDirectRelations($categoryIds=null, $productIds=null)
    {
        $visibilityInfo = $this->_getVisibilityAttributeInfo();
        $statusInfo     = $this->_getStatusAttributeInfo();

        /**
         * Insert direct relations
         * product_ids (enabled filter) X category_ids X store_ids
         * Validate store root category
         */
        $isParent = new Zend_Db_Expr('1');
        $select = $this->_getWriteAdapter()->select()
            ->from(array('cp' => $this->_categoryProductTable),
                array('category_id', 'product_id', 'position', $isParent))
            ->joinInner(array('pw'  => $this->_productWebsiteTable), 'pw.product_id=cp.product_id', array())
            ->joinInner(array('g'   => $this->_groupTable), 'g.website_id=pw.website_id', array())
            ->joinInner(array('s'   => $this->_storeTable), 's.group_id=g.group_id', array('store_id'))
            ->joinInner(array('rc'  => $this->_categoryTable), 'rc.entity_id=g.root_category_id', array())
            ->joinInner(
                array('ce'=>$this->_categoryTable),
                'ce.entity_id=cp.category_id AND ce.path LIKE CONCAT(rc.path, \'/%\')',
                array())
            ->joinLeft(
                array('dv'=>$visibilityInfo['table']),
                "dv.entity_id=cp.product_id AND dv.attribute_id={$visibilityInfo['id']} AND dv.store_id=0",
                array())
            ->joinLeft(
                array('sv'=>$visibilityInfo['table']),
                "sv.entity_id=cp.product_id AND sv.attribute_id={$visibilityInfo['id']} AND sv.store_id=s.store_id",
                array('visibility' => 'IF(sv.value_id, sv.value, dv.value)'))
            ->joinLeft(
                array('ds'=>$statusInfo['table']),
                "ds.entity_id=cp.product_id AND ds.attribute_id={$statusInfo['id']} AND ds.store_id=0",
                array())
            ->joinLeft(
                array('ss'=>$statusInfo['table']),
                "ss.entity_id=cp.product_id AND ss.attribute_id={$statusInfo['id']} AND ss.store_id=s.store_id",
                array())
            ->where('IF(ss.value_id, ss.value, ds.value)=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);
        if ($categoryIds) {
            $select->where('cp.category_id IN (?)', $categoryIds);
        }
        if ($productIds) {
            $select->where('cp.product_id IN(?)', $productIds);
        }
        $sql = $select->insertFromSelect($this->getMainTable());
        $this->_getWriteAdapter()->query($sql);
        return $this;
    }

    /**
     * Rebuild index for anchor categories and associated t child categories products
     *
     * @param   null | array $categoryIds
     * @param   null | array $productIds
     * @return  Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Indexer_Product
     */
    protected function _refreshAnchorRelations($categoryIds=null, $productIds=null)
    {
        $anchorInfo     = $this->_getAnchorAttributeInfo();
        $visibilityInfo = $this->_getVisibilityAttributeInfo();
        $statusInfo     = $this->_getStatusAttributeInfo();

        /**
         * Insert anchor categories relations
         */
        $isParent = new Zend_Db_Expr('0');
        $position = new Zend_Db_Expr('0');
        $select = $this->_getReadAdapter()->select( )
            ->from(array('ce' => $this->_categoryTable), array('entity_id'))
            ->joinInner(array('pw'  => $this->_productWebsiteTable), '', array('product_id', $position, $isParent))
            ->joinInner(array('g'   => $this->_groupTable), 'g.website_id=pw.website_id', array())
            ->joinInner(array('s'   => $this->_storeTable), 's.group_id=g.group_id', array('store_id'))
            ->joinInner(array('rc'  => $this->_categoryTable), 'rc.entity_id=g.root_category_id', array())
            ->joinLeft(
                array('dca'=>$anchorInfo['table']),
                "dca.entity_id=ce.entity_id AND dca.attribute_id={$anchorInfo['id']} AND dca.store_id=0",
                array())
            ->joinLeft(
                array('sca'=>$anchorInfo['table']),
                "sca.entity_id=ce.entity_id AND sca.attribute_id={$anchorInfo['id']} AND sca.store_id=s.store_id",
                array())
            ->joinLeft(
                array('dv'=>$visibilityInfo['table']),
                "dv.entity_id=pw.product_id AND dv.attribute_id={$visibilityInfo['id']} AND dv.store_id=0",
                array())
            ->joinLeft(
                array('sv'=>$visibilityInfo['table']),
                "sv.entity_id=pw.product_id AND sv.attribute_id={$visibilityInfo['id']} AND sv.store_id=s.store_id",
                array('visibility' => 'IF(sv.value_id, sv.value, dv.value)'))
            ->joinLeft(
                array('ds'=>$statusInfo['table']),
                "ds.entity_id=pw.product_id AND ds.attribute_id={$statusInfo['id']} AND ds.store_id=0",
                array())
            ->joinLeft(
                array('ss'=>$statusInfo['table']),
                "ss.entity_id=pw.product_id AND ss.attribute_id={$statusInfo['id']} AND ss.store_id=s.store_id",
                array())
            /**
             * Condition for anchor or root category (all products should be assigned to root)
             */
            ->where('(ce.path LIKE CONCAT(rc.path, \'/%\') AND IF(sca.value_id, sca.value, dca.value)=1) OR ce.entity_id=rc.entity_id')
            ->where('IF(ss.value_id, ss.value, ds.value)=?', Mage_Catalog_Model_Product_Status::STATUS_ENABLED);

        if ($categoryIds) {
            $select->where('ce.entity_id IN (?)', $categoryIds);
        }
        if ($productIds) {
            $select->where('pw.product_id=?', $productIds);
        }
        $sql = $select->insertFromSelect($this->getMainTable());
        $this->_getWriteAdapter()->query($sql);
        return $this;
    }

    /**
     * Get is_anchor category attribute information
     *
     * @return array array('id' => $id, 'table'=>$table)
     */
    protected function _getAnchorAttributeInfo()
    {
        $isAnchorAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_category', 'is_anchor');
        $info = array(
            'id'    => $isAnchorAttribute->getId() ,
            'table' => $isAnchorAttribute->getBackend()->getTable()
        );
        return $info;
    }

    /**
     * Get visibility product attribute information
     *
     * @return array array('id' => $id, 'table'=>$table)
     */
    protected function _getVisibilityAttributeInfo()
    {
        $visibilityAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'visibility');
        $info = array(
            'id'    => $visibilityAttribute->getId() ,
            'table' => $visibilityAttribute->getBackend()->getTable()
        );
        return $info;
    }

    /**
     * Get status product attribute information
     *
     * @return array array('id' => $id, 'table'=>$table)
     */
    protected function _getStatusAttributeInfo()
    {
        $statusAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'status');
        $info = array(
            'id'    => $statusAttribute->getId() ,
            'table' => $statusAttribute->getBackend()->getTable()
        );
        return $info;
    }

    /**
     * Rebuild all index data
     *
     * @return unknown_type
     */
    public function reindexAll()
    {
        /**
         * Create temporary index table
         */
        $this->cloneIndexTable();
        $idxTable = $this->getIdxTable();
        $idxAdapter = $this->_getIndexAdapter();
        $stores = $this->_getStoresInfo();
        /**
         * Build index for each store
         */
        foreach ($stores as $storeData) {
            $storeId = $storeData['store_id'];
            $websiteId = $storeData['website_id'];
            $rootPath = $storeData['root_path'];
            /**
             * Prepare visibility for all enabled store products
             */
            $enabledTable = $this->_prepareEnabledProductsVisibility($websiteId, $storeId);
            /**
             * Select information about anchor categories
             */
            $anchorTable = $this->_prepareAnchorCategories($storeId, $rootPath);
            /**
             * Add relations between not anchor categories and products
             */
            $sql = "INSERT INTO {$idxTable}
                SELECT
                    cp.category_id, cp.product_id, cp.position, 1, {$storeId}, pv.visibility
                FROM
                    {$this->_categoryProductTable} AS cp
                    INNER JOIN {$enabledTable} AS pv ON pv.product_id=cp.product_id
                    LEFT JOIN {$anchorTable} AS ac ON ac.category_id=cp.category_id
                WHERE
                    ac.category_id IS NULL";
            $idxAdapter->query($sql);
            /**
             * Prepare anchor categories products
             */
            $anchorProductsTable = $this->_resources->getTableName('tmp_category_index_anchor_products');
            $idxAdapter->query('DROP TABLE IF EXISTS ' . $anchorProductsTable);
            $sql = "CREATE TABLE `{$anchorProductsTable}` (
              `category_id` int(10) unsigned NOT NULL DEFAULT '0',
              `product_id` int(10) unsigned NOT NULL DEFAULT '0'
            ) ENGINE=MyISAM";
            $idxAdapter->query($sql);
            $sql = "SELECT
                    STRAIGHT_JOIN DISTINCT
                    ca.category_id, cp.product_id
                FROM {$anchorTable} AS ca
                  INNER JOIN {$this->_categoryTable} AS ce
                    ON ce.path LIKE ca.path
                  INNER JOIN {$this->_categoryProductTable} AS cp
                    ON cp.category_id = ce.entity_id
                  INNER JOIN {$enabledTable} as pv
                    ON pv.product_id = cp.product_id";
            $this->insertFromSelect($sql, $anchorProductsTable, array('category_id' , 'product_id'));
            /**
             * Add anchor categories products to index
             */
            $sql = "INSERT INTO {$idxTable}
                SELECT
                    ap.category_id, ap.product_id, cp.position,
                    IF(cp.product_id, 1, 0), {$storeId}, pv.visibility
                FROM
                    {$anchorProductsTable} AS ap
                    LEFT JOIN {$this->_categoryProductTable} AS cp
                        ON cp.category_id=ap.category_id AND cp.product_id=ap.product_id
                    INNER JOIN {$enabledTable} as pv
                        ON pv.product_id = ap.product_id";
            $idxAdapter->query($sql);
        }
        $this->syncData();
        $tmpTables = array(
            $idxAdapter->quoteIdentifier($idxTable),
            $idxAdapter->quoteIdentifier($enabledTable),
            $idxAdapter->quoteIdentifier($anchorTable),
            $idxAdapter->quoteIdentifier($anchorProductsTable)
        );
        $idxAdapter->query('DROP TABLE IF EXISTS '.implode(',', $tmpTables));
        return $this;
    }

    /**
     * Get array with store|website|root_categry path information
     *
     * @return array
     */
    protected function _getStoresInfo()
    {
        $stores = $this->_getReadAdapter()->fetchAll("
            SELECT
                s.store_id, s.website_id, c.path AS root_path
            FROM
                {$this->getTable('core/store')} AS s,
                {$this->getTable('core/store_group')} AS sg,
                {$this->getTable('catalog/category')} AS c
            WHERE
                sg.group_id=s.group_id
                AND c.entity_id=sg.root_category_id
        ");
        return $stores;
    }

    /**
     * Create temporary table with enabled products visibility info
     *
     * @return string temporary table name
     */
    protected function _prepareEnabledProductsVisibility($websiteId, $storeId)
    {
        $statusAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'status');
        $visibilityAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', 'visibility');
        $statusAttributeId = $statusAttribute->getId();
        $visibilityAttributeId = $visibilityAttribute->getId();
        $statusTable = $statusAttribute->getBackend()->getTable();
        $visibilityTable = $visibilityAttribute->getBackend()->getTable();
        /**
         * Prepare temporary table
         */
        $tmpTable = $this->_resources->getTableName('tmp_category_index_enabled_products');
        $sql = 'DROP TABLE IF EXISTS ' . $tmpTable;
        $this->_getIndexAdapter()->query($sql);
        $sql = "CREATE TABLE {$tmpTable} (
           `product_id` int(10) unsigned NOT NULL DEFAULT '0',
           `visibility` int(11) unsigned NOT NULL DEFAULT '0',
           KEY `IDX_PRODUCT` (`product_id`)
         ) ENGINE=MyISAM";
        $this->_getIndexAdapter()->query($sql);
        $sql = "SELECT
                pw.product_id AS product_id,
                IF(pvs.value_id>0, pvs.value, pvd.value) AS visibility
            FROM
                {$this->_productWebsiteTable} AS pw
                LEFT JOIN {$visibilityTable} AS pvd
                    ON pvd.entity_id=pw.product_id AND pvd.attribute_id={$visibilityAttributeId} AND pvd.store_id=0
                LEFT JOIN {$visibilityTable} AS pvs
                    ON pvs.entity_id=pw.product_id AND pvs.attribute_id={$visibilityAttributeId} AND pvs.store_id={$storeId}
                LEFT JOIN {$statusTable} AS psd
                    ON psd.entity_id=pw.product_id AND psd.attribute_id={$statusAttributeId} AND psd.store_id=0
                LEFT JOIN {$statusTable} AS pss
                    ON pss.entity_id=pw.product_id AND pss.attribute_id={$statusAttributeId} AND pss.store_id={$storeId}
            WHERE
                pw.website_id={$websiteId}
                AND IF(pss.value_id>0, pss.value, psd.value) = " . Mage_Catalog_Model_Product_Status::STATUS_ENABLED;
        $this->insertFromSelect($sql, $tmpTable, array('product_id' , 'visibility'));
        return $tmpTable;
    }

    /**
     * Create temporary table with list of anchor categories
     *
     * @param   int $storeId
     * @return  string temporary table name
     */
    protected function _prepareAnchorCategories($storeId)
    {
        $isAnchorAttribute = Mage::getSingleton('eav/config')->getAttribute('catalog_category', 'is_anchor');
        $anchorAttributeId = $isAnchorAttribute->getId();
        $anchorTable = $isAnchorAttribute->getBackend()->getTable();
        $tmpTable = $this->_resources->getTableName('tmp_category_index_anchor_categories');
        $sql = 'DROP TABLE IF EXISTS ' . $tmpTable;
        $this->_getIndexAdapter()->query($sql);
        $sql = "CREATE TABLE {$tmpTable} (
            `category_id` int(10) unsigned NOT NULL DEFAULT '0',
            `path` varchar(257) CHARACTER SET utf8 NOT NULL DEFAULT '',
            KEY `IDX_CATEGORY` (`category_id`)
        ) ENGINE=MyISAM";
        $this->_getIndexAdapter()->query($sql);
        $sql = "SELECT
            ce.entity_id AS category_id,
            concat(ce.path, '/%') AS path
        FROM
            {$this->_categoryTable} as ce
            LEFT JOIN {$anchorTable} AS cad
                ON cad.entity_id=ce.entity_id AND cad.attribute_id={$anchorAttributeId} AND cad.store_id=0
            LEFT JOIN {$anchorTable} AS cas
                ON cas.entity_id=ce.entity_id AND cas.attribute_id={$anchorAttributeId} AND cas.store_id={$storeId}
        WHERE
            IF(cas.value_id>0, cas.value, cad.value) = 1";
        $this->insertFromSelect($sql, $tmpTable, array('category_id' , 'path'));
        return $tmpTable;
    }
}