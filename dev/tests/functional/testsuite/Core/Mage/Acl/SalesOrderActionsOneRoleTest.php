<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Acl
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * ACL tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_Acl_SalesOrderActionsOneRoleTest extends Mage_Selenium_TestCase
{
    protected function tearDownAfterTest()
    {
        $this->logoutAdminUser();
    }

    /**
     * <p>Precondition Method</p>
     * <p>Creating Simple product and test customer for next tests</p>
     *
     * @return array
     *
     * @test
     */
    public function preconditionsForTestsCreateProduct()
    {
        $this->loginAdminUser();
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_visible');
        $userData = $this->loadDataSet('Customers', 'generic_customer_account');
        $addressData = $this->loadDataSet('SalesOrderActions', 'customer_addresses');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple);
        //Verification
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->navigate('manage_customers');
        $this->customerHelper()->createCustomer($userData, $addressData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_customer');

        return array('sku' => $simple['general_name'], 'email' => $userData['email']);
    }

    /**
     * <p>Precondition Method</p>
     * <p>Create test Role with full permissions for Sales Order Actions</p>
     *
     * @return array $roleSource
     *
     * @test
     * @depends preconditionsForTestsCreateProduct
     */
    public function createRole()
    {
        $this->loginAdminUser();
        $this->navigate('manage_roles');
        $roleSource = $this->loadDataSet('AdminUserRole', 'generic_admin_user_role_acl',
            array('resource_acl' => 'sales_order'));
        $roleSource['role_resources_tab']['role_resources']['resource_2'] = 'sales_orders_invoice';
        $roleSource['role_resources_tab']['role_resources']['resource_3'] = 'sales_orders_ship';
        $roleSource['role_resources_tab']['role_resources']['resource_4'] = 'sales_orders_credit_memos';
        $this->adminUserHelper()->createRole($roleSource);
        $this->assertMessagePresent('success', 'success_saved_role');

        return $roleSource;
    }

    /**
     * <p>Precondition Method</p>
     * <p> Create test Admin user with test Role(Full permissions for order actions) </p>
     *
     * @param array $roleSource
     *
     * @return array $testAdminUser
     *
     * @test
     * @depends createRole
     */
    public function createAdminWithTestRole($roleSource)
    {
        $this->loginAdminUser();
        $this->navigate('manage_admin_users');
        $testAdminUser = $this->loadDataSet('AdminUsers', 'generic_admin_user',
            array('role_name' => $roleSource['role_info_tab']['role_name']));
        $this->adminUserHelper()->createAdminUser($testAdminUser);
        $this->assertMessagePresent('success', 'success_saved_user');

        return $testAdminUser;
    }

    /**
     * <p>Verify count of Top Navigation elements and they children</p>
     *
     * @param array $testAdminUser
     *
     * @test
     * @depends createAdminWithTestRole
     * @TestlinkId TL-MAGE-5967
     */
    public function verifyScopeOneRole($testAdminUser)
    {
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($testAdminUser);
        // Verify that navigation menu has only one element
        $this->assertEquals(1, $this->getControlCount('pageelement', 'navigation_menu_items'),
            'Count of Top Navigation Menu elements not equal 1, should be equal');
        // Verify that navigation menu has only 4 child elements
        $this->assertEquals(2, $this->getControlCount('pageelement', 'navigation_children_menu_items'),
            'Count of child Navigation Menu not equal 4, should be equal 4');
    }

    /**
     * <p>Admin User with full Sales-Order-Action Resources can create order </p>
     *
     * @param array $testAdminUser
     * @param array $orderData
     *
     * @return bool|int
     *
     * @test
     * @depends createAdminWithTestRole
     * @depends preconditionsForTestsCreateProduct
     * @depends verifyScopeOneRole
     * @TestlinkId TL-MAGE-5968
     */
    public function createOrderOneRole($testAdminUser, $orderData)
    {
        $this->markTestSkipped('MAGETWO-7635');
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($testAdminUser);
        $this->navigate('manage_sales_orders');
        $orderCreationData = $this->loadDataSet('SalesOrderActions', 'order_data',
            array('filter_sku' => $orderData['sku'], 'email' => $orderData['email']));
        $this->orderHelper()->createOrder($orderCreationData);
        $this->assertMessagePresent('success', 'success_created_order');
        $this->validatePage('view_order');
        $orderId = $this->orderHelper()->defineOrderId();

        return $orderId;
    }

    /**
     * <p>Admin User with full Sales-Order-Action Resources can create invoice for order</p>
     *
     * @param string $orderId
     * @param array $testAdminUser
     *
     * @depends createOrderOneRole
     * @depends createAdminWithTestRole
     *
     * @test
     * @TestlinkId TL-MAGE-5969
     */
    public function createInvoiceTestOneRole($orderId, $testAdminUser)
    {
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($testAdminUser);
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $this->orderInvoiceHelper()->createInvoiceAndVerifyProductQty();
    }

    /**
     * <p>Admin User with full Sales-Order-Action Resources can Hold order</p>
     *
     * @param $orderId
     * @param array $testAdminUser
     *
     * @depends createOrderOneRole
     * @depends createAdminWithTestRole
     *
     * @test
     * @TestlinkId TL-MAGE-5970
     */
    public function holdOrderOneRole($orderId, $testAdminUser)
    {
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($testAdminUser);
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $this->saveForm('hold');
        $this->assertMessagePresent('success', 'success_hold_order');
    }

    /**
     * <p>Admin User with full Sales-Order-Action Resources can unHold order</p>
     *
     * @param $orderId
     * @param array $testAdminUser
     *
     * @depends createOrderOneRole
     * @depends createAdminWithTestRole
     * @depends holdOrderOneRole
     *
     * @test
     * @TestlinkId TL-MAGE-5971
     */
    public function unHoldOrderOneRole($orderId, $testAdminUser)
    {
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($testAdminUser);
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $this->saveForm('unhold');
        $this->assertMessagePresent('success', 'success_unhold_order');
    }

    /**
     * <p>Admin User with full Sales-Order-Action Resources can create Shipment for order</p>
     *
     * @param array $testAdminUser
     * @param $orderId
     *
     * @depends createOrderOneRole
     * @depends createAdminWithTestRole
     * @depends holdOrderOneRole
     *
     * @test
     * @TestlinkId TL-MAGE-5972
     */
    public function createShippingOneRole($orderId, $testAdminUser)
    {
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($testAdminUser);
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $this->orderShipmentHelper()->createShipmentAndVerifyProductQty();
    }

    /**
     * <p>Admin User with full Sales-Order-Action Resources can create Credit Memo for order</p>
     *
     * @param $orderId
     * @param array $testAdminUser
     *
     * @depends createOrderOneRole
     * @depends createAdminWithTestRole
     * @depends holdOrderOneRole
     *
     * @test
     * @TestlinkId TL-MAGE-5973
     */
    public function createCreditMemoOneRole($orderId, $testAdminUser)
    {
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($testAdminUser);
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $this->orderCreditMemoHelper()->createCreditMemoAndVerifyProductQty('refund_offline');
    }

    /**
     * <p>Admin User with full Sales-Order-Action Resources can create Reorder for order</p>
     *
     * @param bool|int $orderId
     * @param array $testAdminUser
     *
     * @return bool|int
     *
     * @depends createOrderOneRole
     * @depends createAdminWithTestRole
     * @depends holdOrderOneRole
     *
     * @test
     * @TestlinkId TL-MAGE-5974
     */
    public function reorderOrderOneRole($orderId, $testAdminUser)
    {
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($testAdminUser);
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $this->clickButton('reorder');
        $this->orderHelper()->submitOrder();
        $orderId = $this->orderHelper()->defineOrderId();
        $this->assertMessagePresent('success', 'success_created_order');

        return $orderId;
    }

    /**
     * <p>Admin User with full Sales-Order-Action Resources can edit Order(create new)</p>
     *
     * @param $orderId
     * @param array $testAdminUser
     *
     * @return bool|int
     *
     * @depends reorderOrderOneRole
     * @depends createAdminWithTestRole
     *
     * @test
     * @TestlinkId TL-MAGE-5976
     */
    public function editOrderOneRole($orderId, $testAdminUser)
    {
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($testAdminUser);
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $this->clickButtonAndConfirm('edit', 'confirmation_for_edit');
        $this->orderHelper()->submitOrder();
        $orderId = $this->orderHelper()->defineOrderId();
        $this->assertMessagePresent('success', 'success_created_order');

        return $orderId;
    }

    /**
     * <p>Admin User with full Sales-Order-Action Resources can cancel order</p>
     *
     * @param $orderId
     * @param array $testAdminUser
     *
     * @depends editOrderOneRole
     * @depends createAdminWithTestRole
     *
     * @test
     * @TestlinkId TL-MAGE-5975
     */
    public function cancelOrderOneRole($orderId, $testAdminUser)
    {
        $this->admin('log_in_to_admin', false);
        $this->adminUserHelper()->loginAdmin($testAdminUser);
        $this->orderHelper()->openOrder(array('filter_order_id' => $orderId));
        $this->clickButtonAndConfirm('cancel', 'confirmation_for_cancel');
        $this->assertMessagePresent('success', 'success_canceled_order');
    }
}
