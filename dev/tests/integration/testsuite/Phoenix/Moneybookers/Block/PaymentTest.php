<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Phoenix_Moneybookers
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Phoenix_Moneybookers_Block_PaymentTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getMoneybookersLogoSrcDataProvider
     */
    public function testGetMoneybookersLogoSrc($localeCode, $expectedFile)
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        Mage::app()->getLocale()->setLocale($localeCode);
        $block = new Phoenix_Moneybookers_Block_Payment;
        $this->assertStringEndsWith($expectedFile, $block->getMoneybookersLogoSrc());
    }

    /**
     * @return array
     */
    public function getMoneybookersLogoSrcDataProvider()
    {
        return array(
            array('en_US', 'banner_120_int.gif'),
            array('de_DE', 'banner_120_de.png'),
            array('br_PT', 'banner_120_int.gif'),
        );
    }
}
