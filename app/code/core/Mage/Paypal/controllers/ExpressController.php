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
 * Express Checkout Controller
 *
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Mage_Paypal_ExpressController extends Mage_Core_Controller_Front_Action
{
    /**
     * When there's an API error
     *
     */
    public function errorAction()
    {
        $this->_redirect('checkout/cart');
    }

    public function cancelAction()
    {
        $this->_redirect('checkout/cart');
    }

    /**
     * Get singleton with paypal express order transaction information
     *
     * @return Mage_Paypal_Model_Express
     */
    public function getExpress()
    {
        return Mage::getSingleton('paypal/express');
    }

    /**
     * When a customer clicks Paypal button on shopping cart
     *
     */
    public function shortcutAction()
    {
        $this->getExpress()->shortcutSetExpressCheckout();
        $this->getResponse()->setRedirect($this->getExpress()->getRedirectUrl());
    }

    /**
     * When a customer chooses Paypal on Checkout/Payment page
     *
     */
    public function markAction()
    {
        $this->getExpress()->markSetExpressCheckout();
        $this->getResponse()->setRedirect($this->getExpress()->getRedirectUrl());
    }

    public function editAction()
    {
        $this->getResponse()->setRedirect($this->getExpress()->getApi()->getPaypalUrl());
    }

    /**
     * Return here from Paypal before final payment (continue)
     *
     */
    public function returnAction()
    {
        $this->getExpress()->returnFromPaypal();
        $this->getResponse()->setRedirect($this->getExpress()->getRedirectUrl());
    }

    /**
     * Return here from Paypal after final payment (commit) or after on-site order review
     *
     */
    public function reviewAction()
    {
        $this->loadLayout(array('default', 'paypal_express_review'), 'paypal_express_review');
        $this->_initLayoutMessages('paypal/session');
        $this->renderLayout();
    }

    /**
     * Get PayPal Onepage checkout model
     *
     * @return Mage_Paypal_Model_Express_Onepage
     */
    public function getReview()
    {
        return Mage::getSingleton('paypal/express_review');
    }

    public function saveShippingMethodAction()
    {
        if (!$this->getRequest()->isPost()) {
            return;
        }

        $data = $this->getRequest()->getParam('shipping_method', '');
        $result = $this->getReview()->saveShippingMethod($data);

        if ($this->getRequest()->getParam('ajax')) {
            $this->loadLayout('paypal_express_review_details');
            $this->getResponse()->setBody($this->getLayout()->getBlock('root')->toHtml());
        } else {
            $this->_redirect('paypal/express/review');
        }
    }

    public function saveOrderAction()
    {
        $result = $this->getReview()->saveOrder();

        if (!empty($result['success'])) {
            if ($this->getRequest()->getParam('ajax')) {
                $this->getResponse()->setBody('SUCCESS');
            } else {
                $this->_redirect('checkout/onepage/success');
            }
        } else {
            if ($this->getRequest()->getParam('ajax')) {
                $this->getResponse()->setBody(join("\n", $result['error_messages']));
            } else {
                foreach ($result['error_messages'] as $error) {
                    Mage::getSingleton('paypal/session')->addError($error);
                }
                $this->_redirect('paypal/express/review');
            }
        }
    }
}