<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order invoice shipping total calculation model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Dmitriy Soroka <dmitriy.soroka@varien.com>
 */
class Mage_Sales_Model_Order_Invoice_Total_Shipping extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $invoice->setShippingAmount(0);
        $invoice->setBaseShippingAmount(0);

        $orderShippingAmount = $invoice->getOrder()->getShippingAmount();
        $baseOrderShippingAmount = $invoice->getOrder()->getBaseShippingAmount();
        if ($orderShippingAmount) {
            /**
             * Check shipping amount in previus invoices
             */
            foreach ($invoice->getOrder()->getInvoiceCollection() as $previusInvoice) {
            	if ($previusInvoice->getShippingAmount() && !$previusInvoice->isCanceled()) {
            	    return $this;
            	}
            }
            $invoice->setShippingAmount($orderShippingAmount);
            $invoice->setBaseShippingAmount($baseOrderShippingAmount);

            $invoice->setGrandTotal($invoice->getGrandTotal()+$orderShippingAmount);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()+$baseOrderShippingAmount);
        }
        return $this;
    }
}