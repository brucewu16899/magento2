<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2012 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Tests for invoice, shipment and credit memo with gift options
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Enterprise_Mage_Order_GiftWrapping_GiftWrappingTest extends Mage_Selenium_TestCase
{
    public function assertPreconditions()
    {
        $this->loginAdminUser();
    }

    protected function tearDownAfterTest()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($this->loadDataSet('GiftMessage', 'gift_options_disable_all'));
    }

    /**
     * <p>Creating 2 simple products</p>
     *
     * @test
     * @return array
     */
    public function preconditionsCreateProducts()
    {
        $this->navigate('manage_products');
        $product1 = $this->loadDataSet('Product', 'simple_product_visible');
        $product2 = $this->loadDataSet('Product', 'simple_product_visible');
        $this->productHelper()->createProduct($product1);
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->createProduct($product2);
        $this->assertMessagePresent('success', 'success_saved_product');
        return array($product1, $product2);
    }

    /**
     * <p>Create Gift Wrapping for tests</p>
     *
     * @test
     * @return array $giftWrappingData
     */
    public function preconditionsCreateGiftWrapping()
    {
        $giftWrappingData = $this->loadDataSet('GiftWrapping', 'gift_wrapping_without_image');
        $this->navigate('manage_gift_wrapping');
        $this->giftWrappingHelper()->createGiftWrapping($giftWrappingData);
        $this->assertMessagePresent('success', 'success_saved_gift_wrapping');
        return $giftWrappingData;
    }

   /**
    * <p>Preconditions:</p>
    * <p>System -> Sales -> Gift Options (Default scope) -> Switch to "no" following options:</p>
    * <p>"Allow Gift Messages on Order Level";</p>
    * <p>"Allow Gift Messages for Order Items";</p>
    * <p>"Allow Gift Wrapping on Order Level";</p>
    * <p>"Allow Gift Wrapping for Order Items";</p>
    * <p>"Allow Gift Receipt";</p>
    * <p>"Allow Printed Card";</p>

    * <p>System -> Sales -> Gift Options (Website scope) -> Switch to "yes" following options:</p>
    * <p>"Allow Gift Messages on Order Level";</p>
    * <p>"Allow Gift Messages for Order Items";</p>
    * <p>"Allow Gift Wrapping on Order Level";</p>
    * <p>"Allow Gift Wrapping for Order Items";<p>
    * <p>"Allow Gift Receipt";</p>
    * <p>"Allow Printed Card";</p>
    *
    * <p>Steps:</p>
    * <p>1. Log into beckend Sales -> Orders;</p>
    * <p>2. Push "create New Order";</p>
    * <p>3. Select any customer from list;</p>
    * <p>4. Select a Store from list;</p>
    * <p>5. Add at least 2 products uses "Add products" button;</p>
    * <p>6. Enter Billing and shipping addresses;</p>
    * <p>7. Choose Shipping and payment Methods;</p>
    * <p>8. Edit gift messages for entire order and Items individually;</p>
    * <p>9. Push "Submit Order" button;</p>
    * <p>10. Open the created order. Check if all switched in this test case gift options are</p>
    * <p> saved;</p>
    *
    * <p>Expected result:</p>
    * <p>After step 9: Notification massage "The order has been created." apppears</p>
    * <p>After step 10: All switched in this test case gift options are saved</p>
    *
    * @TestlinkId TL-MAGE-861
    * @depends preconditionsCreateProducts
    * @depends preconditionsCreateGiftWrapping
    * @param $productData
    * @param $giftWrappingData
    * @test
    */
    public function giftWrappingBackendWebsite($productData, $giftWrappingData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_options_disable_all');
        $this->systemConfigurationHelper()->configure('gift_options_enable_all_website');
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_gift_options_full',
            array('gift_wrapping_design' => $giftWrappingData['gift_wrapping_design']),
            array('product1' => $productData[0]['general_sku'],
                  'product2' => $productData[1]['general_sku']));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->createOrder($orderData, FALSE);
        //Verification
        $this->assertMessagePresent('success', 'success_created_order');
        $this->orderHelper()->verifyGiftOptions($orderData);
    }

    /**
     * <p>Preconditions:</p>
     * <p>System -> Sales -> Gift Options (Default scope) -> Switch to "yes" following options:</p>
     * <p>"Allow Gift Messages on Order Level";</p>
     * <p>"Allow Gift Messages for Order Items";</p>
     * <p>"Allow Gift Wrapping on Order Level";</p>
     * <p>"Allow Gift Wrapping for Order Items";</p>
     * <p>"Allow Gift Receipt";</p>
     * <p>"Allow Printed Card";</p>

     * <p>System -> Sales -> Gift Options (Website scope) -> Switch to "no" following options:</p>
     * <p>"Allow Gift Messages on Order Level";</p>
     * <p>"Allow Gift Messages for Order Items";</p>
     * <p>"Allow Gift Wrapping on Order Level";</p>
     * <p>"Allow Gift Wrapping for Order Items";<p>
     * <p>"Allow Gift Receipt";</p>
     * <p>"Allow Printed Card";</p>
     *
     * <p>Steps:</p>
     * <p>1. Log into beckend Sales-> Orders;</p>
     * <p>2. Push "create New Order";</p>
     * <p>3. Select any customer from list;</p>
     * <p>4. Select a Store from list;</p>
     * <p>5. Add at least 2 products uses "Add products" button;</p>
     *
     * <p>Expected result:</p>
     * <p>After step 5: "Gift Options" link does not appear under any of the added products;</p>
     * <p>"Gift Options" are not available for the whole order;</p>
     *
     * @TestlinkId TL-MAGE-872
     * @depends preconditionsCreateProducts
     * @param $productData
     * @test
     */
    public function giftWrappingBackendGlobalScope($productData)
    {
        //Preconditions
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure('gift_options_enable_all_default_config');
        $this->systemConfigurationHelper()->configure('gift_options_disable_all_website');
        //Data
        $orderData = $this->loadDataSet('SalesOrder', 'order_gift_options_full', null,
            array('product1' => $productData[0]['general_sku'],
                  'product2' => $productData[1]['general_sku']));
        //Steps
        $this->navigate('manage_sales_orders');
        $this->orderHelper()->navigateToCreateOrderPage(null, $orderData['store_view']);
        $this->fillForm($orderData['account_data']);
        foreach ($orderData['products_to_add'] as $value) {
            $this->orderHelper()->addProductToOrder($value);
        }
        //Verification
        $this->orderHelper()->verifyGiftOptionsDisabled($orderData);
    }
}
