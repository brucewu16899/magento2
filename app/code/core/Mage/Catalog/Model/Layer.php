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
 * @category   Mage
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Catalog view layer model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Model_Layer extends Varien_Object
{

    /**
     * Retrieve current layer product collection
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
     */
    public function getProductCollection()
    {
        $collection = $this->getData('product_collection');
        if (is_null($collection)) {
            $collection = $this->getCurrentCategory()->getProductCollection()
                ->addCategoryFilter($this->getCurrentCategory());
            $this->prepareProductCollection($collection);
            $this->setData('product_collection', $collection);
        }

        return $collection;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection $collection
     * @return Mage_Catalog_Model_Layer
     */
    public function prepareProductCollection($collection)
    {
        $collection->addAttributeToSelect('name')
            ->addAttributeToSelect('url_key')

            ->addAttributeToSelect('price')
            ->addAttributeToSelect('special_price')
            ->addAttributeToSelect('special_from_date')
            ->addAttributeToSelect('special_to_date')
            ->joinMinimalPrice()

            ->addAttributeToSelect('description')
            ->addAttributeToSelect('short_description')

            ->addAttributeToSelect('image')
            ->addAttributeToSelect('thumbnail')
            ->addAttributeToSelect('small_image')

            ->addAttributeToSelect('tax_class_id')

            ->addStoreFilter();

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);

        return $this;
    }

    /**
     * Retrieve current category model
     *
     * @return Mage_Catalog_Model_Category
     */
    public function getCurrentCategory()
    {
        $category = $this->getData('current_category');
        if (is_null($category)) {
            if ($category = Mage::registry('current_category')) {
                $this->setData('current_category', $category);
            }
            else {
                Mage::throwException(Mage::helper('catalog')->__('Cannot retrieve current category object'));
            }
        }
        return $category;
    }

    /**
     * Retrieve current store model
     *
     * @return Mage_Core_Model_Store
     */
    public function getCurrentStore()
    {
        return Mage::app()->getStore();
    }

    /**
     * Enter description here...
     *
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute_Collection
     */
    public function getFilterableAttributes()
    {
        $entity = $this->getProductCollection()->getEntity();
        $setIds = $this->getProductCollection()->getSetIds();
        $collection = Mage::getModel('eav/entity_attribute')->getCollection();
        /* @var $collection Mage_Eav_Model_Mysql4_Entity_Attribute_Collection */
        $collection->getSelect()->distinct(true);
        $collection->setEntityTypeFilter($entity->getConfig()->getId())
            ->setAttributeSetFilter($setIds)
            ->addIsFilterableFilter()
            ->load();
        foreach ($collection as $item) {
            $item->setEntity($entity);
        }

        return $collection;
    }

    /**
     * Retrieve layer state object
     *
     * @return Mage_Catalog_Model_Layer_State
     */
    public function getState()
    {
        $state = $this->getData('state');
        if (is_null($state)) {
            $state = Mage::getModel('catalog/layer_state');
            $this->setData('state', $state);
        }
        return $state;
    }

}
