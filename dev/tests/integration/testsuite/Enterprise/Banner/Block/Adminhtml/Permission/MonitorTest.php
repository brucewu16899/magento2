<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Banner
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Enterprise_Banner
 */
class Enterprise_Banner_Block_Adminhtml_Permission_MonitorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider prepareLayoutDataProvider
     */
    public function testPrepareLayout($blockType, $blockName, $tabsType, $tabsName)
    {
        $layout = new Mage_Core_Model_Layout;
        $layout->addBlock($blockType, $blockName);
        $tabs = $layout->addBlock($tabsType, $tabsName);
        $tab = $layout->addBlock(
            'Enterprise_Banner_Block_Adminhtml_Promo_Catalogrule_Edit_Tab_Banners',
            'banners_section',
            $tabsName
        );
        $tabs->addTab('banners_section', $tab);

        $this->assertContains('banners_section', $tabs->getTabsIds());
        $this->assertTrue($layout->hasElement($blockName));
        $this->assertInstanceOf($blockType, $layout->getBlock($blockName));
        $layout->createBlock('Enterprise_Banner_Block_Adminhtml_Permission_Monitor', 'bannner.permission.monitor');
        $this->assertFalse($layout->hasElement($blockName));
        $this->assertFalse($layout->getBlock($blockName));
        $this->assertNotContains('banners_section', $tabs->getTabsIds());
    }

    public function prepareLayoutDataProvider()
    {
        return array(
            array(
                'Enterprise_Banner_Block_Adminhtml_Promo_Salesrule_Edit_Tab_Banners',
                'salesrule.related.banners',
                'Mage_Adminhtml_Block_Promo_Quote_Edit_Tabs',
                'promo_quote_edit_tabs',
            ),
            array(
                'Enterprise_Banner_Block_Adminhtml_Promo_Salesrule_Edit_Tab_Banners',
                'catalogrule.related.banners',
                'Mage_Adminhtml_Block_Widget_Tabs',
                'promo_catalog_edit_tabs',
            ),
        );
    }
}