<?php
/**
 * Layered navigation filter
 *
 * @package     Mage
 * @subpackage  Catalog
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Catalog_Block_Layer_Filter extends Mage_Core_Block_Template
{
    protected $_filter;
    
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('catalog/layer/filter.phtml');
        $this->_initFilter();
    }
    
    protected function _initFilter()
    {
        return $this;
    }
    
    public function getItems()
    {
        return array();
    }
    
    public function getHtml()
    {
        return parent::toHtml();
    }
    
}
