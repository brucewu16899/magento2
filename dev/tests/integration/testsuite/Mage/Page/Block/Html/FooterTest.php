<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Page
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Page_Block_Html_FooterTest extends PHPUnit_Framework_TestCase
{
    public function testGetCacheKeyInfo()
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        $block = Mage::getModel('Mage_Page_Block_Html_Footer');
        $storeId = Mage::app()->getStore()->getId();
        $this->assertEquals(array('PAGE_FOOTER', $storeId, 0, 'default', 'default', null), $block->getCacheKeyInfo());
    }
}
