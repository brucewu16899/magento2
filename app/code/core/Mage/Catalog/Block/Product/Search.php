<?php



/**
 * Product search result block
 *
 * @package    Mage
 * @module     Catalog
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Block_Product_Search extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
    }

    public function loadByQuery(Zend_Controller_Request_Http $request)
    {
        $this->setTemplate('catalog/search/result.phtml');
        $query = $this->getQuery();
        $queryEscaped = htmlspecialchars($query);

        Mage::registry('action')->getLayout()->getBlock('head.title')->setContents('Search result for: '.$queryEscaped);

        $page = $request->getParam('p',1);
        $prodCollection = Mage::getModel('catalog_resource','product_collection')
            ->distinct(true)
            ->addCategoryFilter(Mage::registry('website')->getArrCategoriesId())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('description')
            ->addSearchFilter($query)
            ->setOrder($request->getParam('order','name'), $request->getParam('dir','asc'))
            ->setCurPage($page)
            ->setPageSize(9)
            ->loadData();

        $this->assign('query', $queryEscaped);
        $this->assign('productCollection', $prodCollection);

        $pageUrl = clone $request;
        $this->assign('pageUrl', $pageUrl);
        
        $sortUrl = clone $request;
        $sortUrl->setParam('p', 1)->setParam('dir', 'asc');
        $this->assign('sortUrl', $sortUrl);
        
        $this->assign('sortValue', $request->getParam('order','name').'_'.$request->getParam('dir','asc'));
    }
    
    public function loadByAttributeOption(Zend_Controller_Request_Http $request)
    {
        $this->setTemplate('catalog/search/attribute.phtml');
        
        $attribute = $request->getParam('attr');
        $attributeValue = $request->getParam('value');

        $page = $request->getParam('p',1);
        
        $prodCollection = Mage::getModel('catalog_resource','product_collection')
            ->distinct(true)
            ->addCategoryFilter(Mage::registry('website')->getArrCategoriesId())
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('description')
            ->addAttributeToSelect($attribute, $attributeValue)
            ->setOrder($request->getParam('order','name'), $request->getParam('dir','asc'))
            ->setCurPage($page)
            ->setPageSize(9)
            ->loadData();

        $this->assign('productCollection', $prodCollection);
        $this->assign('option', Mage::getModel('catalog', 'product_attribute_option')->load($attributeValue));

        $pageUrl = clone $request;
        $this->assign('pageUrl', $pageUrl);
        
        $sortUrl = clone $request;
        $sortUrl->setParam('p', 1)->setParam('dir', 'asc');
        $this->assign('sortUrl', $sortUrl);
        $this->assign('sortValue', $request->getParam('order','name').'_'.$request->getParam('dir','asc'));
    }
    
    public function loadByAdvancedSearch(Zend_Controller_Request_Http $request)
    {
        $this->setTemplate('catalog/search/result.phtml');
        $search = $request->getParam('search', array());
        $request->setParam('search', false);
        
        Mage::registry('action')->getLayout()->getBlock('head.title')->setContents('Advanced search result');

        $page = $request->getParam('p',1);
        $prodCollection = Mage::getModel('catalog_resource','product_collection')
            ->distinct(true)
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('price')
            ->addAttributeToSelect('description')
            ->setOrder($request->getParam('order','name'), $request->getParam('dir','asc'))
            ->setCurPage($page)
            ->setPageSize(9);
        
        if (!empty($search['query'])) {
            $prodCollection->addSearchFilter($search['query']);
        }
        if (!empty($search['category'])) {
            $prodCollection->addCategoryFilter($search['category']);
        }
        else {
            $prodCollection->addCategoryFilter(Mage::registry('website')->getArrCategoriesId());
        }
        if (!empty($search['price'])) {
            
        }
        if (!empty($search['type'])) {
            $prodCollection->addAttributeToSelect('type', $search['type']);
        }
        if (!empty($search['manufacturer'])) {
            $prodCollection->addAttributeToSelect('manufacturer', $search['manufacturer']);
        }
        
        $prodCollection->load();
        
        $this->assign('query', 'Advanced search');
        $this->assign('productCollection', $prodCollection);

        $pageUrl = clone $request;
        $pageUrl->setParam('array', array('search'=>$search));
        $this->assign('pageUrl', $pageUrl);
        
        $sortUrl = clone $request;
        $sortUrl->setParam('p', 1)->setParam('dir', 'asc');
        $sortUrl->setParam('array', array('search'=>$search));
        $this->assign('sortUrl', $sortUrl);
        
        $this->assign('sortValue', $request->getParam('order','name').'_'.$request->getParam('dir','asc'));
    }
}