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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_AmazonPayments
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * AmazonPayments Controller
 * 
 * @category    Mage
 * @package     Mage_AmazonPayments
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_AmazonPayments_AspController extends Mage_Core_Controller_Front_Action
{
    /**
     * Get singleton with payment model AmazonPayments ASP
     *
     * @return object Mage_AmazonPayments_Model_Payment_Asp
     */
    public function getPayment()
    {
        return Mage::getSingleton('amazonpayments/payment_asp');
    }

    /**
     * Get singleton with model checkout session 
     *
     * @return object Mage_Checkout_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * When a customer press "Place Order" button on Checkout/Review page 
     * Redirect customer to Amazon Simple Pay payment interface
     * 
     */
    public function payAction()
    {
        $session = $this->getSession();
        $session->setAmazonAspQuoteId($session->getQuoteId());
        $session->setAmazonAspLastRealOrderId($session->getLastRealOrderId());
        
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getLastRealOrderId());

        $payment = $this->getPayment(); 
        $payment->setOrder($order);
        
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock('amazonpayments/asp_redirect')
                ->setRedirectUrl($payment->getPayRedirectUrl())
                ->setRedirectParams($payment->getPayRedirectParams())
                ->toHtml()
         );
        
        $payment->processEventRedirect();
                
        $session->unsQuoteId();
        $session->unsLastRealOrderId();
    }
    
    /**
     * When a customer successfully returned from Amazon Simple Pay site 
     * Redirect customer to Checkout/Success page 
     * 
     */
    public function returnSuccessAction()
    {   
        $session = $this->getSession();
        
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getAmazonAspLastRealOrderId());

        if ($order->isEmpty()) {
            return false;
        }
        
        $payment = $this->getPayment(); 
        $payment->setOrder($order);
        $payment->processEventReturnSuccess();
        
        $session->setQuoteId($session->getAmazonAspQuoteId(true));
        $session->getQuote()->setIsActive(false)->save();
        $session->setLastRealOrderId($session->getAmazonAspLastRealOrderId(true));
        
        $this->_redirect('checkout/onepage/success');
    }

    /**
     * Customer canceled payment and successfully returned from Amazon Simple Pay site 
     * Redirect customer to Shopping Cart page 
     * 
     */
    public function returnCancelAction()
    {
        $session = $this->getSession();
        $session->setQuoteId($session->getAmazonAspQuoteId(true));
        
        $order = Mage::getModel('sales/order');
        $order->loadByIncrementId($session->getAmazonAspLastRealOrderId());
        
        if ($order->isEmpty()) {
            return false;
        }

        $payment = $this->getPayment(); 
        $payment->setOrder($order);
        $payment->processEventReturnCancel();
                
        $this->_redirect('checkout/cart/');
    }

    /**
     * Amazon Simple Pay service send notification 
     * 
     */
    public function notificationAction()
    {
    	$this->getPayment()->processNotification($this->getRequest()->getParams());
    }
}
