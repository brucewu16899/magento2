<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * List of tagged products
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tag_Block_Product_Result extends Mage_Catalog_Block_Product_Abstract
{
    protected $_productCollection;


    public function getTag()
    {
        return Mage::registry('current_tag');
    }

    protected function _prepareLayout()
    {
        $title = $this->getHeaderText();
        $this->getLayout()->getBlock('head')->setTitle($title);
        $this->getLayout()->getBlock('root')->setHeaderTitle($title);
        return parent::_prepareLayout();
    }

    public function setListOrders() {
        $this->getChildBlock('search_result_list')
            ->setAvailableOrders(array(
                'name' => Mage::helper('Mage_Tag_Helper_Data')->__('Name'),
                'price'=>Mage::helper('Mage_Tag_Helper_Data')->__('Price'))
            );
    }

    public function setListModes() {
        $this->getChildBlock('search_result_list')
            ->setModes(array(
                'grid' => Mage::helper('Mage_Tag_Helper_Data')->__('Grid'),
                'list' => Mage::helper('Mage_Tag_Helper_Data')->__('List'))
            );
    }

    public function setListCollection() {
        $this->getChildBlock('search_result_list')
           ->setCollection($this->_getProductCollection());
    }

    public function getProductListHtml()
    {
        return $this->getChildHtml('search_result_list');
    }

    protected function _getProductCollection()
    {
        if(is_null($this->_productCollection)) {
            $tagModel = Mage::getModel('Mage_Tag_Model_Tag');
            $this->_productCollection = $tagModel->getEntityCollection()
                ->addAttributeToSelect(Mage::getSingleton('Mage_Catalog_Model_Config')->getProductAttributes())
                ->addTagFilter($this->getTag()->getId())
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addMinimalPrice()
                ->addUrlRewrite()
                ->setActiveFilter();
            $this->_productCollection->setVisibility(
                Mage::getSingleton('Mage_Catalog_Model_Product_Visibility')->getVisibleInSiteIds()
            );
        }

        return $this->_productCollection;
    }

    public function getResultCount()
    {
        if (!$this->getData('result_count')) {
            $size = $this->_getProductCollection()->getSize();
            $this->setResultCount($size);
        }
        return $this->getData('result_count');
    }

    public function getHeaderText()
    {
        if( $this->getTag()->getName() ) {
            return Mage::helper('Mage_Tag_Helper_Data')->__("Products tagged with '%s'", $this->escapeHtml($this->getTag()->getName()));
        } else {
            return false;
        }
    }

    public function getSubheaderText()
    {
        return false;
    }

    public function getNoResultText()
    {
        return Mage::helper('Mage_Tag_Helper_Data')->__('No matches found.');
    }
}
