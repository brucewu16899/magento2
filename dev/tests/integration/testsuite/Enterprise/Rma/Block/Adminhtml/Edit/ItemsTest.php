<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Rma
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Rma_Block_Adminhtml_Edit_ItemsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Enterprise/Rma/_files/rma.php
     */
    public function testToHtml()
    {
        $rma = new Enterprise_Rma_Model_Rma;
        $rma->load(1, 'increment_id');
        Mage::register('current_rma', $rma);
        $utility = new Mage_Core_Utility_Layout($this);
        $layout = $utility->getLayoutFromFixture(
            __DIR__ . '/../../../_files/edit.xml',
            array(array('area' => 'adminhtml'))
        );
        $layout->getUpdate()->addHandle('adminhtml_rma_edit')->load();
        $layout->generateXml()->generateElements();
        $layout->addOutputElement('enterprise_rma_edit_tab_items');
        Mage::getDesign()->setArea('adminhtml');
        $this->assertContains('<div id="enterprise_rma_item_edit_grid">', $layout->getOutput());
    }
}
