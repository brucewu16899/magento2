<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Webservice webapi router model
 *
 * @category   Mage
 * @package    Mage_Webapi
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Controller_Router_Rest
{
    /**
     * Routes which are stored in module config files webapi.xml
     *
     * @var array
     */
    protected $_routes = array();

    /** @var Mage_Core_Helper_Abstract */
    protected $_helper;

    /**
     * Initialize helper.
     *
     * @param Mage_Core_Helper_Abstract $helper
     */
    function __construct(Mage_Core_Helper_Abstract $helper = null)
    {
        $this->_helper = $helper ? $helper : Mage::helper('Mage_Webapi_Helper_Data');
    }

    /**
     * Set routes
     *
     * @param array $routes
     * @return Mage_Webapi_Controller_Router_Rest
     */
    public function setRoutes(array $routes)
    {
        $this->_routes = $routes;

        return $this;
    }

    /**
     * Get routes
     *
     * @return array
     */
    public function getRoutes()
    {
        return $this->_routes;
    }

    /**
     * Route the Request, the only responsibility of the class
     * Find route that match current URL, set parameters of the route to Request object
     *
     * @param Mage_Webapi_Controller_RequestAbstract $request
     * @return Mage_Webapi_Controller_Router_Route_Rest
     * @throws Mage_Webapi_Exception
     */
    public function match(Mage_Webapi_Controller_RequestAbstract $request)
    {
        /** @var Mage_Webapi_Controller_Router_Route_Rest $route */
        foreach ($this->getRoutes() as $route) {
            $params = $route->match($request);
            if ($params !== false) {
                // TODO: Try to remove params set to $request
                $request->setParams($params);
                return $route;
            }
        }
        throw new Mage_Webapi_Exception($this->_helper->__('Request does not match any route.'),
            Mage_Webapi_Exception::HTTP_NOT_FOUND);
    }
}
