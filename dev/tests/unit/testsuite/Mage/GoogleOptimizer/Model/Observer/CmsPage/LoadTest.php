<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Observer_CmsPage_LoadTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_codeMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_eventObserverMock;

    /**
     * @var Mage_GoogleOptimizer_Model_Observer_Product_Load
     */
    protected $_model;

    public function setUp()
    {
        $this->_helperMock = $this->getMock('Mage_GoogleOptimizer_Helper_Data', array(), array(), '', false);
        $this->_codeMock = $this->getMock(
            'Mage_GoogleOptimizer_Model_Code', array('getId', 'loadScripts'), array(), '', false
        );
        $this->_eventObserverMock = $this->getMock('Varien_Event_Observer', array(), array(), '', false);

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $this->_model = $objectManagerHelper->getObject('Mage_GoogleOptimizer_Model_Observer_CmsPage_Load', array(
            'helper' => $this->_helperMock, 'modelCode' => $this->_codeMock
        ));
    }

    public function testAppendToCategoryGoogleExperimentScriptSuccess()
    {
        $event = $this->getMock('Varien_Event', array('getObject'), array(), '', false);
        $page = $this->getMock(
            'Mage_Catalog_Model_Category', array('setGoogleExperiment', 'getId'), array(), '', false
        );

        $event->expects($this->once())->method('getObject')->will($this->returnValue($page));

        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));

        $values = array(
            'entity_id' => 3,
            'entity_type' => Mage_GoogleOptimizer_Model_Code::CODE_ENTITY_TYPE_CMS,
            'store_id' => 0
        );

        $this->_codeMock->expects($this->once())->method('loadScripts')
            ->with($values['entity_id'], $values['entity_type'], $values['store_id']);

        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(2));

        $page->expects($this->once())->method('getId')->will($this->returnValue($values['entity_id']));
        $page->expects($this->once())->method('setGoogleExperiment')->with($this->_codeMock);

        $this->_model->appendToCmsPageGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testAppendToCategoryGoogleExperimentScriptFail()
    {
        $event = $this->getMock('Varien_Event', array('getObject'), array(), '', false);
        $page = $this->getMock(
            'Mage_Catalog_Model_Category', array('setGoogleExperiment', 'getId'), array(), '', false
        );

        $event->expects($this->once())->method('getObject')->will($this->returnValue($page));

        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(false));

        $this->_codeMock->expects($this->never())->method('loadScripts');

        $this->_codeMock->expects($this->never())->method('getId');

        $page->expects($this->never())->method('getId');
        $page->expects($this->never())->method('setGoogleExperiment');

        $this->_model->appendToCmsPageGoogleExperimentScript($this->_eventObserverMock);
    }

    public function testAppendToCategoryGoogleExperimentScriptFailSecond()
    {
        $event = $this->getMock('Varien_Event', array('getObject'), array(), '', false);
        $page = $this->getMock(
            'Mage_Catalog_Model_Category', array('setGoogleExperiment', 'getId'), array(), '', false
        );

        $event->expects($this->once())->method('getObject')->will($this->returnValue($page));

        $this->_eventObserverMock->expects($this->once())->method('getEvent')->will($this->returnValue($event));

        $this->_helperMock->expects($this->once())->method('isGoogleExperimentActive')->will($this->returnValue(true));

        $values = array(
            'entity_id' => 3,
            'entity_type' => Mage_GoogleOptimizer_Model_Code::CODE_ENTITY_TYPE_CMS,
            'store_id' => 0
        );

        $this->_codeMock->expects($this->once())->method('loadScripts')
            ->with($values['entity_id'], $values['entity_type'], $values['store_id']);

        $this->_codeMock->expects($this->once())->method('getId')->will($this->returnValue(false));

        $page->expects($this->once())->method('getId')->will($this->returnValue($values['entity_id']));

        $page->expects($this->never())->method('setGoogleExperiment');

        $this->_model->appendToCmsPageGoogleExperimentScript($this->_eventObserverMock);
    }
}