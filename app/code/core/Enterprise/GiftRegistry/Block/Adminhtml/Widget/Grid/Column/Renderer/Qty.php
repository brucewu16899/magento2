<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Column renderer for gift registry item grid qty column
 */
class Enterprise_GiftRegistry_Block_Adminhtml_Widget_Grid_Column_Renderer_Qty
    extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render gift registry item qty as input html element
     *
     * @param  Varien_Object $row
     * @return string
     */
    protected function _getValue(Varien_Object $row)
    {
        $value = $row->getData($this->getColumn()->getIndex()) * 1;

        $html = '<input type="text" ';
        $html .= 'name="items[' . $row->getItemId() . '][' . $this->getColumn()->getId() . ']"';
        $html .= 'value="' . $value . '"';
        $html .= 'class="input-text ' . $this->getColumn()->getInlineCss() . '"/>';
        return $html;
    }
}
