<?php
/**
 * Web API REST response.
 *
 * @copyright {}
 */
class Mage_Webapi_Controller_Response_Rest extends Mage_Webapi_Controller_Response
{
    /**#@+
     * Success HTTP response codes.
     */
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_MULTI_STATUS = 207;
    /**#@-*/

    /** @var Mage_Webapi_Controller_Dispatcher_ErrorProcessor */
    protected $_errorProcessor;

    /** @var Mage_Webapi_Controller_Response_Rest_RendererInterface */
    protected $_renderer;

    /** @var Mage_Core_Model_Logger */
    protected $_logger;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Controller_Response_Rest_Renderer_Factory $rendererFactory
     * @param Mage_Webapi_Controller_Dispatcher_ErrorProcessor $errorProcessor
     * @param Mage_Core_Model_Logger $logger
     * @param Mage_Webapi_Helper_Data $helper
     */
    public function __construct(
        Mage_Webapi_Controller_Response_Rest_Renderer_Factory $rendererFactory,
        Mage_Webapi_Controller_Dispatcher_ErrorProcessor $errorProcessor,
        Mage_Core_Model_Logger $logger,
        Mage_Webapi_Helper_Data $helper
    ) {
        $this->_renderer = $rendererFactory->get();
        $this->_errorProcessor = $errorProcessor;
        $this->_logger = $logger;
        $this->_helper = $helper;
    }

    /**
     * Add exception to the list of exceptions.
     *
     * Replace real error message of untrusted exceptions to prevent potential vulnerability.
     *
     * @param Exception $exception
     * @return Mage_Webapi_Controller_Response_Rest
     */
    public function setException(Exception $exception)
    {
        // TODO: Replace Mage::getIsDeveloperMode() to isDeveloperMode() (Mage_Core_Model_App)
        if ($exception instanceof Mage_Webapi_Exception || Mage::getIsDeveloperMode()) {
            parent::setException($exception);
        } else {
            $this->_logger->logException($exception);
            parent::setException(
                new Mage_Webapi_Exception(
                    $this->_helper->__("Internal Error. Details are available in Magento log file."),
                    Mage_Webapi_Exception::HTTP_INTERNAL_ERROR
                )
            );
        }
        return $this;
    }

    /**
     * Send response to the client, render exceptions if they are present.
     */
    public function sendResponse()
    {
        try {
            if ($this->isException()) {
                $this->_renderMessages();
            }
            parent::sendResponse();
        } catch (Exception $e) {
            // If the server does not support all MIME types accepted by the client it SHOULD send 406 (not acceptable).
            $httpCode = $e->getCode() == Mage_Webapi_Exception::HTTP_NOT_ACCEPTABLE
                ? Mage_Webapi_Exception::HTTP_NOT_ACCEPTABLE
                : Mage_Webapi_Exception::HTTP_INTERNAL_ERROR;

            /** If error was encountered during "error rendering" process then use error renderer. */
            $this->_errorProcessor->renderException($e, $httpCode);
        }
    }

    /**
     * Generate and set HTTP response code, error messages to Response object.
     */
    protected function _renderMessages()
    {
        $formattedMessages = array();
        $formattedMessages['messages'] = $this->getMessages();
        $responseHttpCode = null;
        /** @var Exception $exception */
        foreach ($this->getException() as $exception) {
            $code = ($exception instanceof Mage_Webapi_Exception)
                ? $exception->getCode()
                : Mage_Webapi_Exception::HTTP_INTERNAL_ERROR;
            $messageData = array('code' => $code, 'message' => $exception->getMessage());
            // TODO: Replace Mage::getIsDeveloperMode() to isDeveloperMode() (Mage_Core_Model_App)
            if (Mage::getIsDeveloperMode()) {
                $messageData['trace'] = $exception->getTraceAsString();
            }
            $formattedMessages['messages']['error'][] = $messageData;
            // keep HTTP code for response
            $responseHttpCode = $code;
        }
        // set HTTP code of the last error, Content-Type, and all rendered error messages to body
        $this->setHttpResponseCode($responseHttpCode);
        $this->setMimeType($this->_renderer->getMimeType());
        $this->setBody($this->_renderer->render($formattedMessages));
        return $this;
    }
}
