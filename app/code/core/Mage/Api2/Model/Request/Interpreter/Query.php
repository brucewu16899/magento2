<?php
/**
 * {license}
 *
 * @category    Mage
 * @package     Mage_Api2
 */

/**
 * Request content interpreter query adapter
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Request_Interpreter_Query implements Mage_Api2_Model_Request_Interpreter_Interface
{
    /**
     * URI validate pattern
     */
    const URI_VALIDATE_PATTERN = "/^(?:%[[:xdigit:]]{2}|[A-Za-z0-9-_.!~*'()\[\];\/?:@&=+$,])*$/";

    /**
     * Parse request body into array of params
     *
     * @param string $body  Posted content from request
     * @return array        Return always array
     * @throws Exception|Mage_Api2_Exception
     */
    public function interpret($body)
    {
        if (!is_string($body)) {
            throw new Exception(sprintf('Invalid data type "%s". String expected.', gettype($body)));
        }

        if (!$this->_validateQuery($body)) {
            throw new Mage_Api2_Exception(
                'Invalid data type. Check Content-Type.',
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST
            );
        }

        $data = array();
        parse_str($body, $data);
        return $data;
    }

    /**
     * Returns true if and only if the query string passes validation.
     *
     * @param  string $query The query to validate
     * @return boolean
     * @link   http://www.faqs.org/rfcs/rfc2396.html
     */
    protected function _validateQuery($query)
    {
        return preg_match(self::URI_VALIDATE_PATTERN, $query);
    }
}
