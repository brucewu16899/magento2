<?php
/**
 * Product list
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Block_Product_List extends Mage_Core_Block_Template
{
    protected $_productCollection;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/list.phtml');
    }

    protected function _initChildren()
    {
        // add Home breadcrumb
    	if ($breadcrumbBlock = $this->getLayout()->getBlock('breadcrumbs')) {
    	    $breadcrumbBlock->addCrumb('home',
                array('label'=>__('Home'), 'title'=>__('Go to Home Page'), 'link'=>Mage::getBaseUrl()));
    	}
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $this->_productCollection = Mage::getSingleton('catalog/layer')->getProductCollection();
        }
        return $this->_productCollection;
    }

    /**
     * Retrieve loaded category collection
     *
     * @return Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * Retrieve list toolbar HTML
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Retrieve current view mode
     *
     * @return string
     */
    public function getMode()
    {
        return $this->getChild('toolbar')->getCurrentMode();
    }

    protected function _beforeToHtml()
    {
        $toolbar = $this->getLayout()->createBlock('catalog/product_list_toolbar', 'product_list.toolbar')
            ->setCollection($this->_getProductCollection());
        $this->setChild('toolbar', $toolbar);

        $this->_getProductCollection()->load();
        Mage::getModel('review/review')->appendSummary($this->_getProductCollection());
        return parent::_beforeToHtml();
    }

    /**
     * Retrieve
     *
     * @return unknown
     */
    public function getCompareJsObjectName()
    {
    	if($this->getLayout()->getBlock('catalog.compare.sidebar')) {
    		return $this->getLayout()->getBlock('catalog.compare.sidebar')->getJsObjectName();
    	}

    	return false;
    }

    public function setCollection($collection)
    {
        $this->_productCollection = $collection;
        return $this;
    }
}