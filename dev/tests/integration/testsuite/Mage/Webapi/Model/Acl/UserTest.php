<?php
/**
 * Test for Mage_Webapi_Model_Acl_User model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @magentoDataFixture Mage/Webapi/_files/role.php
 */
class Mage_Webapi_Model_Acl_UserTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var Mage_Webapi_Model_Acl_User
     */
    protected $_model;

    /**
     * @var Mage_Webapi_Model_Acl_Role_Factory
     */
    protected $_roleFactory;

    /**
     * Initialize model
     */
    protected function setUp()
    {
        $this->_objectManager = Mage::getObjectManager();
        $this->_roleFactory = $this->_objectManager->get('Mage_Webapi_Model_Acl_Role_Factory');
        $this->_model = $this->_objectManager->create('Mage_Webapi_Model_Acl_User');
    }

    /**
     * Cleanup model instance
     */
    protected function tearDown()
    {
        unset($this->_objectManager, $this->_model);
    }

    /**
     * Test Web API User CRUD
     */
    public function testCRUD()
    {
        $role = $this->_roleFactory->create()->load('test_role', 'role_name');

        $this->_model
            ->setApiKey('Test User Name')
            ->setRoleId($role->getId());

        $crud = new Magento_Test_Entity($this->_model, array('api_key' => '_User_Name_'));
        $crud->testCrud();
    }
}
