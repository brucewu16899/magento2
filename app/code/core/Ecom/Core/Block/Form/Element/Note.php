<?php

#include_once('Ecom/Core/Block/Form/Element/Abstract.php');

/**
 * Form Note block
 *
 * @package    Ecom
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Ecom_Core_Block_Form_Element_Note extends Ecom_Core_Block_Form_Element_Abstract 
{
    public public function __construct($attributes) 
    {
        parent::__construct($attributes);
    }
    
    public function toHtml()
    {
        $html = $this->renderElementLabel();
        $html.= '<span ';
        $html.= $this->_attributesToString(array(
                'id'
               ,'class'
               ,'style'
               ,'onclick'));
        $html.= '>';
        $html.= $this->getAttribute('value');
        $html.= '</span>';
        
        return $html;
    }
}