<?php

class Mage_Core_Model_Mysql4_Website
{
    protected $_read;
    protected $_write;
    protected $_websiteTable;
    
    public function __construct()
    {
        $this->_read = Mage::registry('resources')->getConnection('core_read');
        $this->_write = Mage::registry('resources')->getConnection('core_write');
        $this->_websiteTable = Mage::registry('resources')->getTableName('core_resource', 'website');
    }
    
    public function getWebsiteById($id)
    {
        return $this->_read->fetchRow("select * from ".$this->_websiteTable." where website_id=?", $id);
    }
    
    public function getWebsiteByCode($code)
    {
        return $this->_read->fetchRow("select * from ".$this->_websiteTable." where website_code=?", $code);
    }
}