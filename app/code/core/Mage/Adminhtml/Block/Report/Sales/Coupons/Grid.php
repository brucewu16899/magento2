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
 * Adminhtml coupons report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Sales_Coupons_Grid extends Mage_Adminhtml_Block_Report_Grid_Abstract
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
        if (($this->getFilterData()->getData('report_type') == 'updated_at_order')) {
            return 'Mage_SalesRule_Model_Resource_Report_Updatedat_Collection';
        } else {
            return 'Mage_SalesRule_Model_Resource_Report_Collection';
        }
    }

    protected function _prepareColumns()
    {
        $this->addColumn('period', array(
            'header'            => Mage::helper('Mage_SalesRule_Helper_Data')->__('Period'),
            'index'             => 'period',
            'width'             => 100,
            'sortable'          => false,
            'period_type'       => $this->getPeriodType(),
            'renderer'          => 'Mage_Adminhtml_Block_Report_Sales_Grid_Column_Renderer_Date',
            'totals_label'      => Mage::helper('Mage_SalesRule_Helper_Data')->__('Total'),
            'subtotals_label'   => Mage::helper('Mage_SalesRule_Helper_Data')->__('Subtotal'),
            'html_decorators' => array('nobr'),
        ));

        $this->addColumn('coupon_code', array(
            'header'    => Mage::helper('Mage_SalesRule_Helper_Data')->__('Coupon Code'),
            'sortable'  => false,
            'index'     => 'coupon_code'
        ));

        $this->addColumn('coupon_uses', array(
            'header'    => Mage::helper('Mage_SalesRule_Helper_Data')->__('Number of Uses'),
            'sortable'  => false,
            'index'     => 'coupon_uses',
            'total'     => 'sum',
            'type'      => 'number'
        ));

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }
        $currencyCode = $this->getCurrentCurrencyCode();
        $rate = $this->getRate($currencyCode);

        $this->addColumn('subtotal_amount', array(
            'header'        => Mage::helper('Mage_SalesRule_Helper_Data')->__('Sales Subtotal Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'subtotal_amount',
            'rate'          => $rate,
        ));

        $this->addColumn('discount_amount', array(
            'header'        => Mage::helper('Mage_SalesRule_Helper_Data')->__('Sales Discount Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'discount_amount',
            'rate'          => $rate,
        ));

        $this->addColumn('total_amount', array(
            'header'        => Mage::helper('Mage_SalesRule_Helper_Data')->__('Sales Total Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'total_amount',
            'rate'          => $rate,
        ));

        $this->addColumn('subtotal_amount_actual', array(
            'header'        => Mage::helper('Mage_SalesRule_Helper_Data')->__('Subtotal Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'subtotal_amount_actual',
            'rate'          => $rate,
        ));

        $this->addColumn('discount_amount_actual', array(
            'header'        => Mage::helper('Mage_SalesRule_Helper_Data')->__('Discount Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'discount_amount_actual',
            'rate'          => $rate,
        ));

        $this->addColumn('total_amount_actual', array(
            'header'        => Mage::helper('Mage_SalesRule_Helper_Data')->__('Total Amount'),
            'sortable'      => false,
            'type'          => 'currency',
            'currency_code' => $currencyCode,
            'total'         => 'sum',
            'index'         => 'total_amount_actual',
            'rate'          => $rate,
        ));

        $this->addExportType('*/*/exportCouponsCsv', Mage::helper('Mage_Adminhtml_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportCouponsExcel', Mage::helper('Mage_Adminhtml_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
