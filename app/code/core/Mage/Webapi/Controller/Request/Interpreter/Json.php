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
 * JSON interpreter of Request content.
 *
 * @category    Mage
 * @package     Mage_Webapi
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Webapi_Controller_Request_Interpreter_Json implements Mage_Webapi_Controller_Request_InterpreterInterface
{
    /** @var Mage_Core_Helper_Abstract */
    protected $_helper;

    /**
     * Initialize helper.
     *
     * @param Mage_Core_Helper_Abstract|null $helper
     */
    function __construct(Mage_Core_Helper_Abstract $helper = null)
    {
        $this->_helper = $helper ? $helper : Mage::helper('Mage_Webapi_Helper_Data');
    }

    /**
     * Parse Request body into array of params.
     *
     * @param string $encodedBody Posted content from request.
     * @return array|null Return NULL if content is invalid.
     * @throws InvalidArgumentException
     * @throws Mage_Webapi_Exception If decoding error was encountered.
     */
    public function interpret($encodedBody)
    {
        if (!is_string($encodedBody)) {
            throw new InvalidArgumentException(sprintf('Invalid data type "%s". String expected.',
                gettype($encodedBody)));
        }
        try {
            $decodedBody = Zend_Json::decode($encodedBody);
        } catch (Zend_Json_Exception $e) {
            throw new Mage_Webapi_Exception($this->_helper->__('Decoding error.'),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        if ($encodedBody != 'null' && $decodedBody === null) {
            throw new Mage_Webapi_Exception($this->_helper->__('Decoding error.'),
                Mage_Webapi_Exception::HTTP_BAD_REQUEST);
        }
        return $decodedBody;
    }
}
