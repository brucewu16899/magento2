<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Pbridge API model
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Model_Payment_Method_Pbridge_Api extends Enterprise_Pbridge_Model_Pbridge_Api_Abstract
{
    /**
     * Prepare, merge, encrypt required params for Payment Bridge and payment request params.
     * Return request params as http query string
     *
     * @param array $request
     * @return string
     */
    protected function _prepareRequestParams($request)
    {
        $request['action'] = 'Payments';
        $request['token'] = $this->getMethodInstance()->getPbridgeResponse('token');
        $request = Mage::helper('Enterprise_Pbridge_Helper_Data')->getRequestParams($request);
        $request = array('data' => Mage::helper('Enterprise_Pbridge_Helper_Data')->encrypt(json_encode($request)));
        return http_build_query($request, '', '&');
    }

    public function validateToken($orderId)
    {
        $this->_call(array(
            'client_identifier' => $orderId,
            'payment_action' => 'validate_token'
        ));
        return $this;
    }

    /**
     * Authorize
     *
     * @param Varien_Object $request
     * @return Enterprise_Pbridge_Model_Payment_Method_Pbridge_Api
     */
    public function doAuthorize($request)
    {
        $request->setData('payment_action', 'place');
        $this->_call($request->getData());
        return $this;
    }

    /**
     * Capture
     *
     * @param Varien_Object $request
     * @return Enterprise_Pbridge_Model_Payment_Method_Pbridge_Api
     */
    public function doCapture($request)
    {
        $request->setData('payment_action', 'capture');
        $this->_call($request->getData());
        return $this;
    }

    /**
     * Refund
     *
     * @param Varien_Object $request
     * @return Enterprise_Pbridge_Model_Payment_Method_Pbridge_Api
     */
    public function doRefund($request)
    {
        $request->setData('payment_action', 'refund');
        $this->_call($request->getData());
        return $this;
    }

    /**
     * Void
     *
     * @param Varien_Object $request
     * @return Enterprise_Pbridge_Model_Payment_Method_Pbridge_Api
     */
    public function doVoid($request)
    {
        $request->setData('payment_action', 'void');
        $this->_call($request->getData());
        return $this;
    }
}
