<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Checkout_Block_Adminhtml_Manage_Accordion_ProductsTest extends PHPUnit_Framework_TestCase
{
    /** @var Mage_Core_Block_Abstract */
    protected $_block;

    protected function setUp()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + block');

        parent::setUp();
        $layout = Mage::getModel('Mage_Core_Model_Layout');
        $this->_block = $layout->createBlock('Enterprise_Checkout_Block_Adminhtml_Manage_Accordion_Products');
    }

    protected function tearDown()
    {
        $this->_block = null;
    }

    public function testPrepareLayout()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + block');

        $searchBlock = $this->_block->getChildBlock('search_button');
        $this->assertInstanceOf('Mage_Backend_Block_Widget_Button', $searchBlock);
        $this->assertEquals('checkoutObj.searchProducts()', $searchBlock->getOnclick());
    }
}
