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
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * PayPal Express Checkout Module
 *
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Mage_Paypal_Model_Express extends Mage_Paypal_Model_Abstract
{
    public function catchError()
    {
        if ($this->getApi()->hasError()) {
            $s = Mage::getSingleton('checkout/session');
            $e = $this->getApi()->getError();
            switch ($e['type']) {
                case 'CURL':
                    $s->addError(__('There was an error connecting to Paypal server:').' '.$e['message']);
                    break;

                case 'API':
                    $s->addError(__('There was an error during communication with Paypal:').' '.$e['short_message'].': '.$e['long_message']);
                    break;
            }
        }
        return $this;
    }

    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('paypal/form', $name)
            ->setMethod('paypal_express')
            ->setPayment($this->getPayment());

        return $block;
    }

    public function createInfoBlock($name)
    {
        $block = $this->getLayout()->createBlock('paypal/info', $name)
            ->setPayment($this->getPayment());
        return $block;
    }

    public function shortcutSetExpressCheckout()
    {
        $this->getApi()
            ->setPaymentType(Mage_Paypal_Model_Api_Nvp::PAYMENT_TYPE_AUTH)
            ->setAmount($this->getQuote()->getGrandTotal())
            ->setCurrencyCode($this->getQuote()->getStoreCurrencyCode())
            ->callSetExpressCheckout();

        $this->catchError();

        return $this;
    }

    public function markSetExpressCheckout()
    {
        $this->getApi()
            ->setPaymentType(Mage_Paypal_Model_Api_Nvp::PAYMENT_TYPE_AUTH)
            ->setAmount($this->getQuote()->getGrandTotal())
            ->setCurrencyCode($this->getQuote()->getStoreCurrencyCode())
            ->setShippingAddress($this->getQuote()->getShippingAddress())
            ->callSetExpressCheckout();

        $this->catchError();

        return $this;
    }

    public function returnFromPaypal()
    {
        $this->_getExpressCheckoutDetails();

        switch ($this->getApi()->getUserAction()) {
            case Mage_Paypal_Model_Api_Nvp::USER_ACTION_CONTINUE:
                $this->_prepareOnepageCheckout();
                $this->getApi()->setRedirectUrl(Mage::getUrl('paypal/express/review'));
                break;

            case Mage_Paypal_Model_Api_Nvp::USER_ACTION_COMMIT:
                $this->getApi()->setRedirectUrl(Mage::getUrl('checkout/success'));
                break;
        }
        return $this;
    }

    protected function _getExpressCheckoutDetails()
    {
        $api = $this->getApi();
        if (!$api->callGetExpressCheckoutDetails()) {
            Mage::throwException(__('Problem during communication with PayPal'));
        }
        $q = $this->getQuote();
        $a = $api->getShippingAddress();

        $a->setCountryId(
            Mage::getModel('directory/country')->loadByCode($a->getCountry())->getId()
        );
        $a->setRegionId(
            Mage::getModel('directory/region')->loadByCode($a->getRegion(), $a->getCountryId())->getId()
        );

        $q->getShippingAddress()->importCustomerAddress($a);

        $q->setCheckoutMethod('paypal_express');

        $q->getPayment()
            ->setMethod('paypal_express')
            ->setPaypalCorrelationId($api->getCorrelationId())
            ->setPaypalPayerId($api->getPayerId())
            ->setPaypalPayerStatus($api->getPayerStatus())
        ;

        $q->setCollectShippingRates(true)->collectTotals()->save();

    }

    protected function _prepareOnepageCheckout()
    {
        Mage::getSingleton('checkout/session')->setStepData('shipping_method', 'allow', true);
    }

    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {

    }

    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {

    }
}
