<?php
/**
 * Custom import CSV file field for shipping table rates
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Sergiy Lysak <sergey@varien.com>
 */
class Mage_Adminhtml_Block_System_Config_Form_Field_Import extends Varien_Data_Form_Element_Abstract
{
    public function __construct($data)
    {
        parent::__construct($data);
        $this->setType('file');
    }

    public function getElementHtml()
    {
        $html = '';
        $html .= '<input type="hidden" name="'.$this->getName().'" value="'.time().'"/>';
        $html .= parent::getElementHtml();

        return $html;
    }
}
