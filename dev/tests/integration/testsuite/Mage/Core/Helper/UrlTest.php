<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Helper_UrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Helper_Url
     */
    protected $_helper = null;

    public function setUp()
    {
        $this->_helper = Mage::helper('Mage_Core_Helper_Url');
    }

    protected function tearDown()
    {
        $this->_helper = null;
    }

    public function testGetCurrentUrl()
    {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/fancy_uri';
        $this->assertEquals('http://example.com/fancy_uri', $this->_helper->getCurrentUrl());
    }

    public function testGetCurrentBase64Url()
    {
        $this->assertEquals('aHR0cDovL2xvY2FsaG9zdA,,', $this->_helper->getCurrentBase64Url());
    }

    public function testGetEncodedUrl()
    {
        $this->assertEquals('aHR0cDovL2xvY2FsaG9zdA,,', $this->_helper->getEncodedUrl());
        $this->assertEquals('aHR0cDovL2V4YW1wbGUuY29tLw,,', $this->_helper->getEncodedUrl('http://example.com/'));
    }

    public function testGetHomeUrl()
    {
        $this->assertEquals('http://localhost/index.php/', $this->_helper->getHomeUrl());
    }
}
