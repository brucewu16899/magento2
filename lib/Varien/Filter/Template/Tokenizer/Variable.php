<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Filter
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Template constructions variables tokenizer
 *
 * @category   Varien
 * @package    Varien_Filter
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Varien_Filter_Template_Tokenizer_Variable extends Varien_Filter_Template_Tokenizer_Abstract
{

    /**
     * Tokenize string and return getted variable stack path
     *
     * @return array
     */
    public function tokenize()
    {
        $actions = array();
        $parameterName = '';
        $variableSet = false;
        do {
            if($this->isWhiteSpace()) {
                // Ignore white spaces
                continue;
            } else if($this->char()!='.' && $this->char()!='(') {
                // Property or method name
                $parameterName .= $this->char();
            } else if($this->char()=='(') {
                // Method declaration
                $methodArgs = $this->getMethodArgs();
                $actions[] = array('type'=>'method',
                                   'name'=>$parameterName,
                                   'args'=>$methodArgs);
                $parameterName = '';
            } else if($parameterName!='') {
                // Property or variable declaration
                if($variableSet) {
                    $actions[] = array('type'=>'property',
                                       'name'=>$parameterName);
                } else {
                    $variableSet = true;
                    $actions[] = array('type'=>'variable',
                                       'name'=>$parameterName);
                }
                $parameterName = '';
            }
        } while ($this->next());

        if($parameterName != '' ) {
            if($variableSet) {
                    $actions[] = array('type'=>'property',
                                       'name'=>$parameterName);
            } else {
                $actions[] = array('type'=>'variable',
                                   'name'=>$parameterName);
            }
        }

        return $actions;
    }

    /**
     * Get string value for method args
     *
     * @return string
     */
    public function getString() {

        $value = '';
        if($this->isWhiteSpace()) {
            return $value;
        }
        $qouteStart = $this->isQuote();

        if($qouteStart) {
           $breakSymbol = $this->char();
        } else {
           $breakSymbol = false;
           $value .= $this->char();
        }

        while ($this->next()) {
            if (!$breakSymbol && ($this->isWhiteSpace() || $this->char() == ',' || $this->char() == ')') ) {
                $this->prev();
                break;
            } else if ($breakSymbol && $this->char() == $breakSymbol) {
                break;
            } else if ($this->char() == '\\') {
                $this->next();
                $value .= $this->char();
            } else {
                $value .= $this->char();

            }
        }

        return $value;
    }

    /**
     * Return true if current char is a number
     *
     * @return boolean
     */
    public function isNumeric() {
        return $this->char() >= '0' && $this->char() <= '9';
    }

    /**
     * Return true if current char is qoute or apostroph
     *
     * @return boolean
     */
    public function isQuote() {
        return $this->char() == '"' || $this->char() == "'";
    }

    /**
     * Return array of arguments for method.
     *
     * @return array
     */
    public function getMethodArgs() {
        $value = array();
        $numberStr = '';

        while($this->next() && $this->char() != ')') {
            if($this->isWhiteSpace() || $this->char() == ',') {
                continue;
            } else if($this->isNumeric()) {
                $value[] = $this->getNumber();
            } else {
                $value[] = $this->getString();
            }
        }

        return $value;
    }

    /**
     * Return number value for method args
     *
     * @return float
     */
    public function getNumber() {
        $value = $this->char();
        while( ($this->isNumeric() || $this->char()=='.') && $this->next() ) {
            $value.= $this->char();
        }

        if(!$this->isNumeric()) {
            $this->prev();
        }

        return floatval($value);
    }

}