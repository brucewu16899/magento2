<?php

set_include_path(get_include_path().PS.Mage::getBaseDir('base').DS.'lib'.DS.'googlecheckout');

require_once('googleresponse.php');
require_once('googlemerchantcalculations.php');
require_once('googleresult.php');
require_once('googlerequest.php');

define('RESPONSE_HANDLER_ERROR_LOG_FILE', 'googleerror.log');
define('RESPONSE_HANDLER_LOG_FILE', 'googlemessage.log');

abstract class Mage_GoogleCheckout_Model_Api_Xml_Abstract extends Varien_Object
{
    public function log($text, $nl=true)
    {
        error_log($text.($nl?"\n":''), 3, '/home/moshe/dev/test/callback.log');
    }

    public function getMerchantId()
    {
        if (!$this->hasData('merchant_id')) {
            $this->setData('merchant_id', Mage::getStoreConfig('google/checkout/merchant_id'));
        }
        return $this->getData('merchant_id');
    }

    public function getMerchantKey()
    {
        if (!$this->hasData('merchant_key')) {
            $this->setData('merchant_key', Mage::getStoreConfig('google/checkout/merchant_key'));
        }
        return $this->getData('merchant_key');
    }

    public function getServerType()
    {
        if (!$this->hasData('server_type')) {
            $this->setData('server_type', Mage::getStoreConfig('google/checkout/sandbox') ? "sandbox" : "");
        }
        return $this->getData('server_type');
    }

    public function getLocale()
    {
        if (!$this->hasData('locale')) {
            $this->setData('locale', Mage::getStoreConfig('google/checkout/locale'));
        }
        return $this->getData('locale');
    }

    public function getCurrency()
    {
        if (!$this->hasData('currency')) {
            $this->setData('currency', $this->getLocale()=='en_US' ? 'USD' : 'GBP');
        }
        return $this->getData('currency');
    }

    /**
     * Google Checkout Request instance
     *
     * @return GoogleRequest
     */
    public function getGRequest()
    {
        if (!$this->hasData('g_request')) {
            $this->setData('g_request', new GoogleRequest(
                $this->getMerchantId(),
                $this->getMerchantKey(),
                $this->getServerType(),
                $this->getCurrency()
            ));

            //Setup the log file
            $this->getData('g_request')->SetLogFiles(
                RESPONSE_HANDLER_ERROR_LOG_FILE,
                RESPONSE_HANDLER_LOG_FILE,
                L_ALL
            );
        }
        return $this->getData('g_request');
    }

    /**
     * Google Checkout Response instance
     *
     * @return GoogleResponse
     */
    public function getGResponse()
    {
        if (!$this->hasData('g_response')) {
            $this->setData('g_response', new GoogleResponse(
                $this->getMerchantId(),
                $this->getMerchantKey()
            ));

            //Setup the log file
            $this->getData('g_response')->SetLogFiles(
                RESPONSE_HANDLER_ERROR_LOG_FILE,
                RESPONSE_HANDLER_LOG_FILE,
                L_ALL
            );
        }
        return $this->getData('g_response');
    }

    protected function _getBaseApiUrl()
    {
        $url = 'https://';
        if ($this->getServerType()=='sandbox') {
            $url .= 'sandbox.google.com/checkout/api/checkout/v2/';
        } else {
            $url .= 'checkout.google.com/api/checkout/v2/';
        }
        return $url;
    }

    abstract protected function _getApiUrl();

    public function _call($xml)
    {
        $auth = 'Basic '.base64_encode($this->getMerchantId().':'.$this->getMerchantKey());

        $headers = array(
            'Authorization: '.$auth,
            'Content-Type: application/xml;charset=UTF-8',
            'Accept: application/xml;charset=UTF-8',
        );

        $url = $this->_getApiUrl();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\r\n".$xml;

        if (Mage::getStoreConfig('google/checkout/debug')) {
            $debug = Mage::getModel('googlecheckout/api_debug');
            $debug->setDir('out')->setUrl($url)->setRequestBody($xml)->save();
        }

        $http = new Varien_Http_Adapter_Curl();
        $http->write('POST', $url, '1.1', $headers, $xml);
        $response = $http->read();
        $response = preg_split('/^\r?$/m', $response, 2);
        $response = trim($response[1]);

        if (!empty($debug)) {
            $debug->setResponseBody($response)->save();
        }

        $result = new SimpleXmlElement($response);
        if ($result->getName()=='error') {
            $this->setError((string)$result->{'error-message'});
            $this->setWarnings((array)$result->{'warning-messages'});
        } else {
            $this->unsError()->unsWarnings();
        }

        $this->setResult($result);

        return $result;
    }

    protected function _getCallbackUrl()
    {
        return Mage::getUrl('googlecheckout/api');
    }
}