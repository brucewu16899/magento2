<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Controller_Context extends Mage_Core_Controller_Varien_Action_Context
{
    /**
     * @var Mage_Backend_Model_Session
     */
    protected $_session;

    /**
     * @var Mage_Backend_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_Core_Model_Authorization
     */
    protected $_authorization;

    /**
     * @var Mage_Core_Model_Translate
     */
    protected $_translator;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param Mage_Backend_Model_Session $session
     * @param Mage_Backend_Helper_Data $helper
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Core_Model_Authorization $authorization
     * @param Mage_Core_Model_Translate $translator
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Layout_Factory $layoutFactory,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Backend_Model_Session $session,
        Mage_Backend_Helper_Data $helper,
        Mage_Core_Model_Authorization $authorization,
        Mage_Core_Model_Translate $translator
    ) {
        parent::__construct($request, $response, $objectManager, $frontController, $layoutFactory, $eventManager);
        $this->_session = $session;
        $this->_helper = $helper;
        $this->_authorization = $authorization;
        $this->_translator = $translator;
    }

    /**
     * @return \Mage_Backend_Helper_Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }

    /**
     * @return \Mage_Backend_Model_Session
     */
    public function getSession()
    {
        return $this->_session;
    }

    /**
     * @return \Mage_Core_Model_Authorization
     */
    public function getAuthorization()
    {
        return $this->_authorization;
    }

    /**
     * @return \Mage_Core_Model_Translate
     */
    public function getTranslator()
    {
        return $this->_translator;
    }
}
