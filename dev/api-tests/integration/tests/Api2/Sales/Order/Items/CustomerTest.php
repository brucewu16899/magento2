<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Test
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for order item (customer) API2
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Sales_Order_Items_CustomerTest extends Magento_Test_Webservice_Rest_Customer
{
    /**
     * Delete fixtures
     */
    protected function tearDown()
    {
        Magento_Test_Webservice::deleteFixture('customer_order', true);
        Magento_Test_Webservice::deleteFixture('customer_quote', true);
        $fixtureProducts = $this->getFixture('customer_products');
        if ($fixtureProducts && count($fixtureProducts)) {
            foreach ($fixtureProducts as $fixtureProduct) {
                $this->callModelDelete($fixtureProduct, true);
            }
        }

        Magento_Test_Webservice::deleteFixture('order', true);
        Magento_Test_Webservice::deleteFixture('quote', true);
        $fixtureProducts = $this->getFixture('products');
        if ($fixtureProducts && count($fixtureProducts)) {
            foreach ($fixtureProducts as $fixtureProduct) {
                $this->callModelDelete($fixtureProduct, true);
            }
        }

        parent::tearDown();
    }

    /**
     * Test get order items for customer
     *
     *  @magentoDataFixture Api2/Sales/_fixtures/customer_order.php
     */
    public function testGetOrder()
    {
        /* @var $fixtureOrder Mage_Sales_Model_Order */
        $fixtureOrder = $this->getFixture('customer_order');
        $restResponse = $this->callGet('orders/' . $fixtureOrder->getId() . '/items');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_OK, $restResponse->getStatus());

        $responseData = $restResponse->getBody();
        $this->assertNotEmpty($responseData);

        $orderItemsIds = array();
        foreach ($responseData as $orderItem) {
            $orderItemsIds[] = $orderItem['item_id'];
        }
        /* @var $fixtureItems Mage_Sales_Model_Resource_Order_Item_Collection */
        $fixtureItems = $fixtureOrder->getItemsCollection();
        foreach ($fixtureItems as $fixtureItem) {
            $this->assertContains($fixtureItem->getId(), $orderItemsIds, 'Order item should be in response');
        }
    }

    /**
     * Test retrieving items for not existing order
     */
    public function testGetItemsForUnavailableOrder()
    {
        $restResponse = $this->callGet('orders/invalid_id/items');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }

    /**
     * Test get order items if customer is not owner
     *
     * @magentoDataFixture Api2/Sales/_fixtures/order.php
     */
    public function testGetOrderIfCustomerIsNotOwner()
    {
        /* @var $fixtureOrder Mage_Sales_Model_Order */
        $fixtureOrder = $this->getFixture('order');
        $restResponse = $this->callGet('orders/' . $fixtureOrder->getId() . '/items');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $restResponse->getStatus());
    }
}
