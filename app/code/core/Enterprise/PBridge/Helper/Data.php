<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_PBridge
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Pbridge helper
 *
 * @category    Enterprise
 * @package     Enterprise_PBridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_PBridge_Helper_Data extends Enterprise_Enterprise_Helper_Core_Abstract
{
    /**
     * Payment Bridge action name to fetch Payment Bridge payment gateways
     *
     * @var string
     */
    const PAYMENT_GATEWAYS_CHOOSER_ACTION = 'GatewaysChooser';

    /**
     * Payment Bridge payment methods available for the current merchant
     *
     * $var array
     */
    protected $_pbridgeAvailableMethods = array();

    /**
     * Check if Payment Bridge Magento Module is enabled in configuration
     *
     * @return boolean
     */
    public function isEnabled()
    {
        return (bool)(Mage::getStoreConfigFlag('payment/pbridge/active'));
    }

    /**
     * Getter
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return Mage::getSingleton('checkout/session')->getQuote();
    }

    /**
     * Prepare and return Payment Bridge request url with parameters
     *
     * @return string
     */
    protected function _preparePbridgeRequestUrl()
    {
        $pbridgeUrl = Mage::getStoreConfig('payment/pbridge/gatewayurl');
        $merchantCode = Mage::getStoreConfig('payment/pbridge/merchantcode');
        $merchantKey  = Mage::getStoreConfig('payment/pbridge/merchantkey');
        $sourceUrl = rtrim($pbridgeUrl, '/') . '/bridge.php?action=' . self::PAYMENT_GATEWAYS_CHOOSER_ACTION
            . '&merchant_code=' . $merchantCode
            . '&merchant_key=' . $merchantKey;
        if ($availableMethods = $this->getPbridgeAvailableMethods()) {
            $sourceUrl .= '&available_methods=' . implode($availableMethods);
        }
        if ($this->_getQuote()) {
            $payment = $this->_getQuote()->getPayment();
            if ($payment && $payment->getMethod()) {
                $sourceUrl .= '&quote_id=' . $this->_getQuote()->getId();
                if ($payment->getMethodInstance()->getToken()) {
                    $sourceUrl .= '&token=' . $payment->getMethodInstance()->getToken();
                }
            }
        }
        return $sourceUrl;
    }

    /**
     * Prepare given payment method and return Payment Bridge payment methods
     * available for the current merchant
     *
     * @param string $method
     * @return array
     */
    protected function _preparePbridgeAvailableMethod($method)
    {
        if (!in_array($method, $this->_pbridgeAvailableMethods)) {
            if (Mage::getStoreConfigFlag('payment/' . $method . '/using_pbridge')) {
                $this->_pbridgeAvailableMethods[] = $method;
            }
        }
        return $this->_pbridgeAvailableMethods;
    }

    /**
     * Getter.
     * Retrieve Payment Bridge url with required parameters
     *
     * @return string
     */
    public function getPbridgeUrl()
    {
        return $this->_preparePbridgeRequestUrl();
    }

    /**
     * Getter
     * Retrieve Payment Bridge payment methods available for the current merchant
     *
     * @return array
     */
    public function getPbridgeAvailableMethods()
    {
        return $this->_pbridgeAvailableMethods;
    }

    /**
     * Check if the payment method is withing the list of available for current merchant
     *
     * @param string $method
     * @return bool
     */
    public function isAvailablePbridgeMethod($method)
    {
        return in_array($method, $this->_preparePbridgeAvailableMethod($method));
    }
}
