<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Report Flag Model
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Reports_Model_Flag extends Mage_Core_Model_Flag
{
    const REPORT_ORDER_FLAG_CODE    = 'report_order_aggregated';
    const REPORT_TAX_FLAG_CODE      = 'report_tax_aggregated';
    const REPORT_SHIPPING_FLAG_CODE = 'report_shipping_aggregated';
    const REPORT_INVOICE_FLAG_CODE  = 'report_invoiced_aggregated';
    const REPORT_REFUNDED_FLAG_CODE = 'report_refunded_aggregated';
    const REPORT_COUPONS_FLAG_CODE  = 'report_coupons_aggregated';
    const REPORT_BESTSELLERS_FLAG_CODE = 'report_bestsellers_aggregated';

    /**
     * Setter for flag code
     *
     * @param string $code
     * @return Mage_Reports_Model_Flag
     */
    public function setReportFlagCode($code)
    {
        $this->_flagCode = $code;
        return $this;
    }
}
