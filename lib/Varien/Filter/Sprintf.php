<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Filter
 * @copyright  {copyright}
 * @license    {license_link}
 */


class Varien_Filter_Sprintf implements Zend_Filter_Interface
{
    protected $_format = null;
    protected $_decimals = null;
    protected $_decPoint = null;
    protected $_thousandsSep = null;
    
    public function __construct($format, $decimals=null, $decPoint='.', $thousandsSep=',')
    {
        $this->_format = $format;
        $this->_decimals = $decimals;
        $this->_decPoint = $decPoint;
        $this->_thousandsSep = $thousandsSep;
    }
    
    public function filter($value)
    {
        if (!is_null($this->_decimals)) {
            $value = number_format($value, $this->_decimals, $this->_decPoint, $this->_thousandsSep);
        }
        $value = sprintf($this->_format, $value);
        return $value;
    }
}