<?php
class Mage_Cart_Model_Mysql4 extends Mage_Core_Model_Db
{
    function __construct()
    {
        parent::__construct();

        $this->_read = $this->_getConnection('cart_read');
        $this->_write = $this->_getConnection('cart_write');
    }

}