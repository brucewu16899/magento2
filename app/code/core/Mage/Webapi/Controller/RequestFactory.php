<?php
/**
 * Factory of web API requests.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_RequestFactory
{
    /**
     * List of request classes corresponding to API types.
     *
     * @var array
     */
    protected $_apiTypeToRequestMap = array(
        Mage_Webapi_Controller_Front::API_TYPE_REST => 'Mage_Webapi_Controller_Request_Rest',
        Mage_Webapi_Controller_Front::API_TYPE_SOAP => 'Mage_Webapi_Controller_Request',
    );

    /** @var Magento_ObjectManager */
    protected $_objectManager;

    /** @var Mage_Webapi_Controller_Front */
    protected $_webApiFrontController;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Controller_Front $webApiFrontController
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        Mage_Webapi_Controller_Front $webApiFrontController,
        Magento_ObjectManager $objectManager
    ) {
        $this->_webApiFrontController = $webApiFrontController;
        $this->_objectManager = $objectManager;
    }

    /**
     * Create request object.
     *
     * Use current API type to define proper request class.
     *
     * @return Mage_Webapi_Controller_Request
     * @throws LogicException If there is no corresponding request class for current API type.
     */
    public function get()
    {
        $apiType = $this->_webApiFrontController->determineApiType();
        if (!isset($this->_apiTypeToRequestMap[$apiType])) {
            throw new LogicException('There is no corresponding request class for the "%s" API type.', $apiType);
        }
        $requestClass = $this->_apiTypeToRequestMap[$apiType];
        return $this->_objectManager->get($requestClass, array('apiType' => $apiType));
    }
}
