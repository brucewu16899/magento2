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

class Mage_Core_Block_TemplateTest extends PHPUnit_Framework_TestCase
{
    public function testGetTemplateFile()
    {
        $design = $this->getMock('Mage_Core_Model_View_DesignInterface');
        $template = 'fixture';
        $area = 'areaFixture';
        $arguments = array(
            'design' => $design,
            'data'   => array('template' => $template, 'area' => $area),
        );
        $helper = new Magento_Test_Helper_ObjectManager($this);

        $block = $helper->getObject('Mage_Core_Block_Template', $arguments);

        $params = array('module' => 'Mage_Core', 'area' => $area);
        $design->expects($this->once())->method('getFilename')->with($template, $params);
        $block->getTemplateFile();
    }

    /**
     * @param string $filename
     * @param string $expectedOutput
     * @dataProvider fetchViewDataProvider
     */
    public function testFetchView($filename, $expectedOutput)
    {
        $map = array(
            array(Mage_Core_Model_Dir::APP, __DIR__),
            array(Mage_Core_Model_Dir::THEMES, __DIR__ . 'design'),
        );
        $dirMock = $this->getMock('Mage_Core_Model_Dir', array(), array(), '', false, false);
        $dirMock->expects($this->any())->method('getDir')->will($this->returnValueMap($map));
        $layout = $this->getMock('Mage_Core_Model_Layout', array('isDirectOutput'), array(), '', false);
        $filesystem = new Magento_Filesystem(new Magento_Filesystem_Adapter_Local);
        $design = $this->getMock('Mage_Core_Model_View_DesignInterface', array(), array(), '', false);
        $translator = $this->getMock('Mage_Core_Model_Translate', array(), array(), '', false);

        $arguments = array(
            'design'        => $design,
            'layout'        => $layout,
            'dirs'          => $dirMock,
            'filesystem'    => $filesystem,
            'translator'    => $translator,
        );
        $helper = new Magento_Test_Helper_ObjectManager($this);

        $block = $this->getMock(
            'Mage_Core_Block_Template',
            array('getShowTemplateHints'),
            $helper->getConstructArguments('Mage_Core_Block_Template', $arguments)
        );
        $layout->expects($this->once())->method('isDirectOutput')->will($this->returnValue(false));

        $this->assertSame($block, $block->assign(array('varOne' => 'value1', 'varTwo' => 'value2')));
        $this->assertEquals($expectedOutput, $block->fetchView(__DIR__ . "/_files/{$filename}"));
    }

    /**
     * @return array
     */
    public function fetchViewDataProvider()
    {
        return array(
            array('template_test_assign.phtml', 'value1, value2'),
            array('invalid_file', ''),
        );
    }
}
