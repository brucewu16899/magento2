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
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

function unnamedErrorHandler($errno, $errstr, $errfile, $errline)
{
    echo __FILE__;
    echo '<pre>';
    var_dump($errno, $errstr, $errfile, $errline);
    echo '</pre>';
    exit;


}
set_error_handler('unnamedErrorHandler', E_ALL|E_STRICT);


/**
 * Webservice api2 abstract
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Server
{
    /**
     * Api2 REST type
     */
    const API_TYPE_REST = 'rest';

    /**#@+
     * HTTP Response Codes
     */
    const HTTP_OK                 = 200;
    const HTTP_BAD_REQUEST        = 400;
    const HTTP_UNAUTHORIZED       = 401;
    const HTTP_FORBIDDEN          = 403;
    const HTTP_NOT_FOUND          = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_NOT_ACCEPTABLE     = 406;
    const HTTP_INTERNAL_ERROR     = 500;
    /**#@- */

    /**
     * List of api types
     *
     * @var array
     */
    protected static $_apiTypes = array(self::API_TYPE_REST);

    /**
     * Run server, the only public method of the server
     *
     * @return void
     */
    public function run()
    {
        /** @var $request Mage_Api2_Model_Request */
        $request = Mage::getSingleton('api2/request');
        /** @var $response Mage_Api2_Model_Response */
        $response = Mage::getSingleton('api2/response');

        /** @var $renderer Mage_Api2_Model_Renderer_Interface */
        $renderer = Mage_Api2_Model_Renderer::factory($request->getAcceptTypes());

        try {
            /** @var $apiUser Mage_Api2_Model_Auth_User_Abstract */
            $apiUser = $this->_authenticate($request);

            $this->_route($request)
                ->_allow($request, $apiUser)
                ->_dispatch($request, $response, $apiUser);

            //NOTE: At this moment Renderer already could have some content rendered, so we should replace it
            if ($response->isException()) {
                throw new Mage_Api2_Exception('Unhandled simple errors.', Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
            }
        } catch (Exception $e) {
            $this->_catchCritical($request, $response, $e, $renderer);
        }

        $response->sendResponse();
    }

    /**
     * Authenticate user
     *
     * @param Mage_Api2_Model_Request $request
     * @return Mage_Api2_Model_Auth_User_Abstract
     */
    protected function _authenticate(Mage_Api2_Model_Request $request)
    {
        /** @var $authManager Mage_Api2_Model_Auth */
        $authManager = Mage::getModel('api2/auth');

        return $authManager->authenticate($request);
    }

    /**
     * Set all routes of the given api type to Route object
     * Find route that match current URL, set parameters of the route to Request object
     *
     * @param Mage_Api2_Model_Request $request
     * @return Mage_Api2_Model_Server
     */
    protected function _route(Mage_Api2_Model_Request $request)
    {
        /** @var $router Mage_Api2_Model_Router */
        $router = Mage::getModel('api2/router');

        $router->routeApiType($request, true)
            ->setRoutes($this->_getConfig()->getRoutes($request->getApiType()))
            ->route($request);

        return $this;
    }

    /**
     * @param Mage_Api2_Model_Request $request
     * @param Mage_Api2_Model_Auth_User_Abstract $apiUser
     * @return Mage_Api2_Model_Server
     * @throws Mage_Api2_Exception
     */
    protected function _allow(Mage_Api2_Model_Request $request, Mage_Api2_Model_Auth_User_Abstract $apiUser)
    {
        $resourceType = $request->getResourceType();
        $operation = $request->getOperation();

        /** @var $globalAcl Mage_Api2_Model_Acl_Global */
        $globalAcl = Mage::getModel('api2/acl_global');
        $isAllowed = $globalAcl->isAllowed($apiUser, $resourceType, $operation);

        if (!$isAllowed) {
            throw new Mage_Api2_Exception('Authorization error.', self::HTTP_FORBIDDEN);
        }

        return $this;
    }

    /**
     * Load class file, instantiate resource class, set parameters to the instance, run resource internal dispatch
     * method
     *
     * @param Mage_Api2_Model_Request $request
     * @param Mage_Api2_Model_Response $response
     * @param Mage_Api2_Model_Auth_User_Abstract $apiUser
     * @return Zend_Controller_Response_Http
     */
    protected function _dispatch(
        Mage_Api2_Model_Request $request,
        Mage_Api2_Model_Response $response,
        Mage_Api2_Model_Auth_User_Abstract $apiUser
    )
    {
        /** @var $dispatcher Mage_Api2_Model_Dispatcher */
        $dispatcher = Mage::getModel('api2/dispatcher');
        $dispatcher->setApiUser($apiUser)->dispatch($request, $response);

        return $response;
    }

    /**
     * Get api2 config instance
     *
     * @return Mage_Api2_Model_Config
     */
    protected function _getConfig()
    {
        return Mage::getModel('api2/config');
    }

    /**
     * Process thrown exception
     * Generate and set HTTP response code, error message to Response object
     *
     * @param Mage_Api2_Model_Request $request
     * @param Zend_Controller_Response_Http $response
     * @param Exception $critical
     * @param Mage_Api2_Model_Renderer_Interface $renderer
     * @return Mage_Api2_Model_Server
     */
    protected function _catchCritical(Mage_Api2_Model_Request $request, Zend_Controller_Response_Http $response,
        Exception $critical, Mage_Api2_Model_Renderer_Interface $renderer)
    {
        //if developer mode is set $critical can be without a Code, it will result in a
        //Zend_Controller_Response_Exception('Invalid HTTP response code')

        //if exception is not Mage_Api2_Exception we can't be sure it contains valid HTTP error code, so we change it
        //to 500;
        $code = ($critical instanceof Mage_Api2_Exception) ? $critical->getCode() : self::HTTP_INTERNAL_ERROR;

        //TODO should we call $response->clearHeaders()
        try {
            //add last error to stack and get the stack
            $response->setException($critical);
            $exceptions = $response->getException();

            //TODO implement domain usage
            $domain = 'api2';

            $messages = array();
            /** @var Exception $exception */
            foreach ($exceptions as $exception) {
                $message = array(
                    'domain'  => $domain,
                    'code'    => $exception->getCode(),
                    'message' => $exception->getMessage(),
                    'trace'   => $exception->getTraceAsString()
                );
                $messages['messages']['error'][] = $message;
            }
            $content = $renderer->render($messages);

            //set HTTP Code of last error, Content-Type and Body
            $response->setHttpResponseCode($code);
            $response->setHeader('Content-Type',
                sprintf('%s; charset=%s', $renderer->getMimeType(), Mage_Api2_Model_Request::REQUEST_CHARSET)
            );
            $response->setBody($content);
        } catch (Exception $e) {
            //tunnelling of 406(Not acceptable) error
            $httpCode = $e->getCode() == self::HTTP_NOT_ACCEPTABLE    //$e->getCode() can result in one more loop
                    ? self::HTTP_NOT_ACCEPTABLE                      // of try..catch
                    : self::HTTP_INTERNAL_ERROR;

            //if error appeared in "error rendering" process then show it in plain text
            $response->setHttpResponseCode($httpCode);
            $response->setHeader('Content-Type', 'text/plain; charset=UTF-8');
            $response->setBody($e->getMessage());
        }

        return $this;
    }

    /**
     * Retrieve api types
     *
     * @static
     * @return array
     */
    public static function getApiTypes()
    {
        return self::$_apiTypes;
    }
}
