<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Paypal\Controller;

/**
 * @magentoDataFixture Magento/Sales/_files/order.php
 */
class PayflowadvancedTest extends \Magento\TestFramework\TestCase\AbstractController
{
    protected function setUp()
    {
        parent::setUp();

        $order = $this->_objectManager->create('Magento\Sales\Model\Order');
        $order->load('100000001', 'increment_id');
        $order->getPayment()->setMethod(\Magento\Paypal\Model\Config::METHOD_PAYFLOWADVANCED);

        $quote = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Sales\Model\Quote'
        )->setStoreId(
            $order->getStoreId()
        )->save();

        $order->setQuoteId($quote->getId());
        $order->save();

        $session = $this->_objectManager->create('Magento\Checkout\Model\Session');
        $session->setLastRealOrderId($order->getRealOrderId())->setLastQuoteId($order->getQuoteId());
    }

    public function testCancelPaymentActionIsContentGenerated()
    {
        $this->dispatch('paypal/payflowadvanced/cancelpayment');
        $this->assertContains(
            "parent.jQuery('#checkoutSteps').trigger('gotoSection', 'payment');",
            $this->getResponse()->getBody()
        );
        $this->assertContains("parent.jQuery('#checkout-review-submit').show();", $this->getResponse()->getBody());
        $this->assertContains("parent.jQuery('#iframe-warning').hide();", $this->getResponse()->getBody());
    }

    public function testReturnurlActionIsContentGenerated()
    {
        $this->dispatch('paypal/payflowadvanced/returnurl');
        $this->assertContains(
            "parent.jQuery('#checkoutSteps').trigger('gotoSection', 'payment');",
            $this->getResponse()->getBody()
        );
        $this->assertContains("parent.jQuery('#checkout-review-submit').show();", $this->getResponse()->getBody());
        $this->assertContains("parent.jQuery('#iframe-warning').hide();", $this->getResponse()->getBody());
    }

    public function testFormActionIsContentGenerated()
    {
        $this->dispatch('paypal/payflowadvanced/form');
        $this->assertContains(
            '<form id="token_form" method="POST" action="https://payflowlink.paypal.com/">',
            $this->getResponse()->getBody()
        );
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order.php
     * @magentoConfigFixture current_store payment/paypal_payflow/active 1
     * @magentoConfigFixture current_store paypal/general/business_account merchant_2012050718_biz@example.com
     */
    public function testCancelAction()
    {
        $order = $this->_objectManager->create('Magento\Sales\Model\Order');
        $session = $this->_objectManager->get('Magento\Checkout\Model\Session');

        $quote = $this->_objectManager->create('Magento\Sales\Model\Quote');
        $quote->load('test02', 'reserved_order_id');
        $order->load('100000001', 'increment_id')->setQuoteId($quote->getId())->save();
        $session->setQuoteId($quote->getId());
        $session->setPaypalStandardQuoteId($quote->getId())->setLastRealOrderId('100000001');
        $this->dispatch('paypal/payflow/cancelpayment');

        $order->load('100000001', 'increment_id');
        $this->assertEquals('canceled', $order->getState());
        $this->assertEquals($session->getQuote()->getGrandTotal(), $quote->getGrandTotal());
        $this->assertEquals($session->getQuote()->getItemsCount(), $quote->getItemsCount());
    }
}
