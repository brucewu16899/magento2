<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Sales_Block_Status_Grid_Column_Unassign extends Mage_Backend_Block_Widget_Grid_Column
{
    /**
     * @var Mage_Sales_Helper_Data
     */
    protected $_helper;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_Sales_Helper_Data $helper
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_Sales_Helper_Data $helper,
        array $data = array()
    ) {
        parent::__construct ($context, $data);

        $this->_helper = $helper;
    }

    /**
     * Add to column decorate action
     *
     * @return array
     */
    public function getFrameCallback()
    {
        return array($this, 'decorateAction');
    }

    /**
     * Decorate column values
     *
     * @param $value
     * @param $row
     * @param $column
     * @param $isExport
     * @return string
     */
    public function decorateAction($value, $row, $column, $isExport)
    {
        $cell = '';
        $state = $row->getState();
        if (!empty($state)) {
            $url = $this->getUrl(
                '*/*/unassign',
                array('status' => $row->getStatus(), 'state' => $row->getState())
            );
            $label = $this->_helper->__('Unassign');
            $cell = '<a href="' . $url . '">' . $label . '</a>';
        }
        return $cell;
    }
}
