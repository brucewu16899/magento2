<?php

/**
 * Product View block
 *
 * @package    Mage
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Block_Product_View extends Mage_Core_Block_Template
{
    protected $_request;
    
    public function __construct()
    {
        parent::__construct();
        $this->_request = Mage::registry('controller')->getRequest();
        $this->setTemplate('catalog/product/view.phtml');
    }

    public function loadData()
    {
        $categoryId = $this->_request->getParam('category', false);
        $productId  = $this->_request->getParam('id');
        
        $product = Mage::getModel('catalog/product')
            ->load($productId)
            ->setCategoryId($categoryId);
        
        $breadcrumbs = $this->getLayout()
            ->createBlock('catalog/breadcrumbs', 'catalog.breadcrumbs');
        $breadcrumbs->addCrumb('home', 
            array('label'=>__('Home'), 'title'=>__('Go to home page'), 'link'=>Mage::getBaseUrl())
        );
        $breadcrumbs->addCrumb('category', 
            array('label'=>$product->getCategoryName(), 'title'=>'', 'link'=>$product->getCategoryUrl())
        );
        $breadcrumbs->addCrumb('product', 
            array('label'=>$product->getName())
        );
        
        $this->setChild('breadcrumbs', $breadcrumbs);
        
        $this->assign('product', $product);
        $this->assign('customerIsLogin', Mage::getSingleton('customer/session')->isLoggedIn());
        
        $this->assign('reviewList', $this->getLayout()->createBlock('review/list')->toHtml());
        $this->assign('reviewForm', $this->getLayout()->createBlock('review/form')->toHtml());
        
        return $this;
    }
}