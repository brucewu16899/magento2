<?php
/**
 * @category   Mage
 * @package    Core
 * @subpackage Model
 */
class Mage_Core_Model_LayoutTest extends PHPUnit_Framework_TestCase
{
    protected $_layout;
    
    public function __construct() {
        $this->_layout = Mage::getSingleton('core', 'layout');
    }
    
    public function setUp() {
    }
    
    public function tearDown() {
    }
    
    public function testCreateBlock()
    {
        // empty name
        $block1 = $this->_layout->createBlock('tpl');
        $this->assertType('object', $block1);
        
        // with name
        $block2 = $this->_layout->createBlock('tpl', 'test_block');
        $this->assertType('object', $block2);
        $this->assertEquals('test_block', $block2->getName());
        
        // with attributes
        $block3 = $this->_layout->createBlock('tpl', 'test_block1', array('testAttr'=>'test  '));
        $this->assertType('object', $block3);
    }
    
    public function testGetBlock()
    {
        $block = $this->_layout->getBlock('test_block');
        $this->assertType('object', $block);
    }
    
    public function testAllBlockOperation()
    {
        $this->_layout->createBlock('tpl', 'tmp_block');
        $this->_layout->removeBlock('tmp_block');
        $block = $this->_layout->getBlock('tmp_block');
        
        var_dump($block);
    }
}