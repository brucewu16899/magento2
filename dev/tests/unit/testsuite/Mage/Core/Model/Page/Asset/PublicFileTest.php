<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Page_Asset_PublicFileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Page_Asset_PublicFile
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_design;

    protected function setUp()
    {
        $this->_design = $this->getMock('Mage_Core_Model_View_DesignInterface');
        $this->_object = new Mage_Core_Model_Page_Asset_PublicFile($this->_design, 'test/style.css', 'css');
    }

    public function testGetUrl()
    {
        $url = 'http://127.0.0.1/magento/test/style.css';
        $this->_design
            ->expects($this->once())
            ->method('getPublicFileUrl')
            ->with('test/style.css')
            ->will($this->returnValue($url))
        ;
        $this->assertEquals($url, $this->_object->getUrl());
    }

    public function testGetContentType()
    {
        $this->assertEquals('css', $this->_object->getContentType());
    }

    public function testGetSourceFile()
    {
        $this->assertSame('test/style.css', $this->_object->getSourceFile());
    }
}
