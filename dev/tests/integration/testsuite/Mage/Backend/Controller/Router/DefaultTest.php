<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Backend_Controller_Router_DefaultTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Backend_Controller_Router_Default
     */
    protected $_model;

    protected function setUp()
    {
        $options = array(
            'area' => 'adminhtml',
            'base_controller' => 'Mage_Backend_Controller_ActionAbstract'
        );
        $this->_model = new Mage_Backend_Controller_Router_Default($options);
        $this->_model->setFront(new Mage_Core_Controller_Varien_Front());
    }

    /**
     * @covers Mage_Backend_Controller_Router_Default::collectRoutes
     */
    public function testCollectRoutes()
    {
        $this->_model->collectRoutes('admin', 'admin');
        $this->assertEquals('admin', $this->_model->getFrontNameByRoute('adminhtml'));
    }

    /**
     * @covers Mage_Backend_Controller_Router_Default::fetchDefault
     */
    public function testFetchDefault()
    {
        $default = array(
            'module' => '',
            'controller' => 'index',
            'action' => 'index'
        );
        $this->assertEmpty($this->_model->getFront()->getDefault());
        $this->_model->fetchDefault();
        $this->assertEquals($default, $this->_model->getFront()->getDefault());
    }

    /**
     * @param string $module
     * @param string $controller
     * @param string $fileName
     *
     * @covers Mage_Backend_Controller_Router_Default::getControllerFileName
     * @dataProvider getControllerFileNameDataProvider
     */
    public function testGetControllerFileName($module, $controller, $fileName)
    {
        $file = $this->_model->getControllerFileName($module, $controller);
        $this->assertStringEndsWith($fileName, $file);
    }

    public function getControllerFileNameDataProvider()
    {
        return array(
            array('Mage_Adminhtml', 'index', 'Adminhtml' . DS . 'controllers' . DS . 'IndexController.php'),
            array(
                'Mage_Index',
                'process',
                'Index' . DS . 'controllers' . DS . 'Adminhtml' . DS . 'ProcessController.php'
            ),
            array(
                'Mage_Index_Adminhtml',
                'process',
                'Index' . DS . 'controllers' . DS . 'Adminhtml' . DS . 'ProcessController.php'
            ),
        );
    }

    /**
     * @param string $module
     * @param string $controller
     * @param string $className
     *
     * @covers Mage_Backend_Controller_Router_Default::getControllerClassName
     * @dataProvider getControllerClassNameDataProvider
     */
    public function testGetControllerClassName($module, $controller, $className)
    {
        $this->assertEquals($className, $this->_model->getControllerClassName($module, $controller));
    }

    public function getControllerClassNameDataProvider()
    {
        return array(
            array('Mage_Adminhtml', 'index', 'Mage_Adminhtml_IndexController'),
            array('Mage_Index', 'process', 'Mage_Index_Adminhtml_ProcessController'),
            array('Mage_Index_Adminhtml', 'process', 'Mage_Index_Adminhtml_ProcessController'),
        );
    }
}
