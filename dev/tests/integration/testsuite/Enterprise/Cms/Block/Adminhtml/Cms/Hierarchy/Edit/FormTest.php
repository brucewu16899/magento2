<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Edit_FormTest extends Mage_Backend_Area_TestCase
{
    /** @var Mage_Core_Model_Layout */
    protected $_layout = null;

    /** @var Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Edit_Form */
    protected $_block = null;

    protected function setUp()
    {
        parent::setUp();
        $this->_layout = Mage::getModel('Mage_Core_Model_Layout');
        $this->_block = $this->_layout->createBlock('Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Edit_Form');
    }

    public function testGetGridJsObject()
    {
        $parentName = 'parent';
        $mockClass = $this->getMockClass('Mage_Catalog_Block_Product_Abstract', array('_prepareLayout'),
            array(Mage::getModel('Mage_Core_Block_Template_Context'))
        );
        $this->_layout->createBlock($mockClass, $parentName);
        $this->_layout->setChild($parentName, $this->_block->getNameInLayout(), '');

        $pageGrid = $this->_layout->addBlock(
            'Enterprise_Cms_Block_Adminhtml_Cms_Hierarchy_Edit_Form_Grid',
            'cms_page_grid',
            $parentName
        );
        $this->assertEquals($pageGrid->getJsObjectName(), $this->_block->getGridJsObject());
    }
}
