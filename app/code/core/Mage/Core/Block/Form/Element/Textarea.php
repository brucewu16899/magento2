<?php



/**
 * Form textarea
 *
 * @package    Mage
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Block_Form_Element_Textarea extends Mage_Core_Block_Form_Element_Abstract 
{
	public public function __construct($attributes) 
	{
		parent::__construct($attributes);
	}
	
	public function toHtml()
	{
	    $html = $this->renderElementLabel();
	    $html.= '<textarea ';
	    $html.= $this->_attributesToString(array(
                'name'
               ,'id'
               ,'cols'
               ,'rows'
               ,'title'
               ,'accesskey'
               ,'tabindex'
               ,'class'
               ,'style'
               ,'disabled'
               ,'readonly'
               ,'onclick'
               ,'onchange'
               ,'onselect'
               ,'onfocus'
               ,'onblur'));

	    $html.= '>';
	    $html.= $this->getAttribute('value');
	    $html.= '</textarea>';
	    
	    return $html;
	}
}