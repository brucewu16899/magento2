<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml shipping report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Sales_Shipping_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract
{
    protected $_columnGroupBy = 'period';

    public function __construct()
    {
        parent::__construct();
        $this->setCountTotals(true);
        $this->setCountSubTotals(true);
    }

    public function getResourceCollectionName()
    {
        return ($this->getFilterData()->getData('report_type') == 'created_at_shipment')
            ? 'Mage_Sales_Model_Resource_Report_Shipping_Collection_Shipment'
            : 'Mage_Sales_Model_Resource_Report_Shipping_Collection_Order';
    }

    protected function _prepareColumns()
    {
        $this->addColumn('period', array(
            'header'            => Mage::helper('Mage_Sales_Helper_Data')->__('Period'),
            'index'             => 'period',
            'width'             => 100,
            'sortable'          => false,
            'period_type'       => $this->getPeriodType(),
            'renderer'          => 'Mage_Adminhtml_Block_Report_Sales_Grid_Column_Renderer_Date',
            'totals_label'      => Mage::helper('Mage_Sales_Helper_Data')->__('Total'),
            'subtotals_label'   => Mage::helper('Mage_Sales_Helper_Data')->__('Subtotal'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('shipping_description', array(
            'header'    => Mage::helper('Mage_Sales_Helper_Data')->__('Carrier/Method'),
            'index'     => 'shipping_description',
            'sortable'  => false
        ));

        $this->addColumn('orders_count', array(
            'header'    => Mage::helper('Mage_Sales_Helper_Data')->__('Number of Orders'),
            'index'     => 'orders_count',
            'total'     => 'sum',
            'type'      => 'number',
            'width'     => 100,
            'sortable'  => false
        ));

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }

        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);

        $this->addColumn('total_shipping', array(
            'header'        => Mage::helper('Mage_Sales_Helper_Data')->__('Total Sales Shipping'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_shipping',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ));

        $this->addColumn('total_shipping_actual', array(
            'header'        => Mage::helper('Mage_Sales_Helper_Data')->__('Total Shipping'),
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'index'         => 'total_shipping_actual',
            'total'         => 'sum',
            'sortable'      => false,
            'rate'          => $rate,
        ));

        $this->addExportType('*/*/exportShippingCsv', Mage::helper('Mage_Adminhtml_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportShippingExcel', Mage::helper('Mage_Adminhtml_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}



