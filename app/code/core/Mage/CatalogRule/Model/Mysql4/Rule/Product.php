<?php


class Mage_CatalogRule_Model_Mysql4_Rule_Product extends Mage_Core_Model_Mysql4_Abstract 
{
    protected function _construct()
    {
        $this->_init('catalogrule/rule_product', 'product_rule_id');
    }
}