<?php

#include_once "Varien/DataObject.php";

class Ecom_Catalog_Model_Mysql4_Product extends Varien_DataObject 
{
    public function load($id)
    {
        $dbConnection = Ecom::getModel('catalog');
        $product_table  = $dbConnection->getTableName('catalog_read', 'product');
        $extension_table= $dbConnection->getTableName('catalog_read', 'product_extension');
        
        $select = $dbConnection->getReadConnection()->select();
        $select->from($product_table, '*')
               ->join($extension_table, $extension_table.'.product_id='.$product_table.'.product_id', '*')
               ->where($product_table.'.product_id=? and '.$extension_table.'.website_id=1', $id);
        
        $this->_data = $dbConnection->getReadConnection()->fetchRow($select);
        return $this;
    }

    public function getLink()
    {
        $url = Ecom::getBaseUrl().'/catalog/product/view/id/'.$this->getProduct_Id();
        return $url;
    }
    
    public function getCategoryLink()
    {
        $url = Ecom::getBaseUrl().'/catalog/category/view/id/'.$this->getCategory_Id();
        return $url;
    }
    
    public function getCategoryName()
    {
        $category = Ecom::getModel('catalog', 'categories')->getNode($this->getCategory_Id());
        return $category->getData('name');
    }
    
    public function getLargeImageLink()
    {
        return Ecom::getBaseUrl().'/catalog/product/image/id/'.$this->getProduct_Id();
    }
}