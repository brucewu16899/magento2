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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Invoice view form
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Invoice_View_Form extends Mage_Adminhtml_Block_Sales_Order_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('sales/order/invoice/view/form.phtml');
        $this->setOrder($this->getInvoice()->getOrder());
    }

    /**
     * Prepare child blocks
     *
     * @return Mage_Adminhtml_Block_Sales_Order_Invoice_Create_Items
     */
    protected function _prepareLayout()
    {
        $infoBlock = $this->getLayout()->createBlock('adminhtml/sales_order_view_info')
            ->setOrder($this->getInvoice()->getOrder());
        $this->setChild('order_info', $infoBlock);

        $totalsBlock = $this->getLayout()->createBlock('adminhtml/sales_order_totals')
            ->setOrder($this->getInvoice()->getOrder())
            ->setSource($this->getInvoice());
        $this->setChild('totals', $totalsBlock);

        $commentsBlock = $this->getLayout()->createBlock('adminhtml/sales_order_comments_view')
            ->setEntity($this->getInvoice());
        $this->setChild('comments', $commentsBlock);

        $paymentInfoBlock = $this->getLayout()->createBlock('adminhtml/sales_order_payment')
            ->setPayment($this->getInvoice()->getOrder()->getPayment());
        $this->setChild('payment_info', $paymentInfoBlock);
        return parent::_prepareLayout();
    }

    /**
     * Retrieve invoice model instance
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function getInvoice()
    {
        return Mage::registry('current_invoice');
    }

    public function getOrderUrl()
    {
        return $this->getUrl('*/sales_order/view', array('order_id'=>$this->getInvoice()->getOrderId()));
    }

    public function formatPrice($price)
    {
        return $this->getInvoice()->getOrder()->formatPrice($price);
    }
}