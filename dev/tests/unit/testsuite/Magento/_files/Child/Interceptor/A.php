<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Magento_Test_Di_Child_Interceptor_A
{
    protected $_wrapperSym;

    public function __construct($wrapperSym = 'A')
    {
        $this->_wrapperSym = $wrapperSym;
    }

    public function wrapBefore($param)
    {
        return $this->_wrapperSym . $param . $this->_wrapperSym;
    }

    /**
     * @param string $returnValue
     * @return string
     */
    public function wrapAfter($returnValue)
    {
        return '_' . $this->_wrapperSym . '_' . $returnValue . '_' . $this->_wrapperSym . '_';
    }
}
