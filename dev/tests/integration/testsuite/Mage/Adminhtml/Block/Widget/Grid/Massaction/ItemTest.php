<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Adminhtml_Block_Widget_Grid_Massaction_ItemTest extends PHPUnit_Framework_TestCase
{
    public function testGetAdditionalActionBlock()
    {
        $this->markTestIncomplete('Need to fix DI dependencies + block');

        /** @var $layout Mage_Core_Model_Layout */
        $layout = Mage::getModel('Mage_Core_Model_Layout');
        $block = $layout->createBlock('Mage_Adminhtml_Block_Widget_Grid_Massaction_Item', 'block');
        $expected = $layout->addBlock('Mage_Core_Block_Template', 'additional_action', 'block');
        $this->assertSame($expected, $block->getAdditionalActionBlock());
    }
}
