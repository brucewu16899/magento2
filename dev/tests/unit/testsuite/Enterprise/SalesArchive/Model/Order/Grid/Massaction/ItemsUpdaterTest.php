<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_SalesArchive
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_SalesArchive_Model_Order_Grid_Massaction_ItemsUpdaterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_cfgSalesArchiveMock;
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_authorizationMock;

    /**
     * @var Enterprise_SalesArchive_Model_Order_Grid_Massaction_ItemsUpdater
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_updateArgs;

    protected function setUp()
    {
        $this->_cfgSalesArchiveMock = $this->getMockBuilder('Enterprise_SalesArchive_Model_Config')
            ->disableOriginalConstructor()
            ->getMock();

        $this->_authorizationMock = $this->getMockBuilder('Magento_AuthorizationInterface')
            ->getMock();

        $this->_model = new Enterprise_SalesArchive_Model_Order_Grid_Massaction_ItemsUpdater(
            $this->_cfgSalesArchiveMock, $this->_authorizationMock
        );

        $this->_updateArgs = array(
            'add_order_to_archive' => array(
                'label' => 'Move to Archive',
                'url' => '*/sales_archive/massAdd'
            ),
            'cancel_order' => array(
                'label' => 'Cancel',
                'url' => '*/sales_archive/massCancel'
            )
        );
    }

    public function testConfigNotActive()
    {
        $this->_cfgSalesArchiveMock->expects($this->any())
            ->method('isArchiveActive')
            ->will($this->returnValue(false));

        $this->assertEquals($this->_updateArgs, $this->_model->update($this->_updateArgs));
    }

    public function testAuthAllowed()
    {
        $this->_cfgSalesArchiveMock->expects($this->any())
            ->method('isArchiveActive')
            ->will($this->returnValue(true));

        $this->_authorizationMock->expects($this->any())
            ->method('isAllowed')
            ->with('Enterprise_SalesArchive::add', null)
            ->will($this->returnValue(true));

        $updatedArgs = $this->_model->update($this->_updateArgs);
        $this->assertArrayHasKey('add_order_to_archive', $updatedArgs);
    }

    public function testAuthNotAllowed()
    {
        $this->_cfgSalesArchiveMock->expects($this->any())
            ->method('isArchiveActive')
            ->will($this->returnValue(true));

        $this->_authorizationMock->expects($this->any())
            ->method('isAllowed')
            ->with('Enterprise_SalesArchive::add', null)
            ->will($this->returnValue(false));

        $updatedArgs = $this->_model->update($this->_updateArgs);
        $this->assertArrayNotHasKey('add_order_to_archive', $updatedArgs);
    }

}
