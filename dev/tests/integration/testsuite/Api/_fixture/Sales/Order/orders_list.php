<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

define('COUNT_ORDERS_LIST', 3);

$ordersList = array();
for ($i = 0; $i < COUNT_ORDERS_LIST; $i++) {
    /* @var $order Mage_Sales_Model_Order */
    $order = Mage::getModel('Mage_Sales_Model_Order')
        ->setBillingAddress(Mage::getModel('Mage_Sales_Model_Order_Address'));

    /* @var $payment Mage_Sales_Model_Order_Payment */
    $payment = Mage::getModel('Mage_Sales_Model_Order_Payment')
        ->setMethod('free')
        ->setOrder($order)
        ->place();

    $order->setPayment($payment); // WARNING: setPayment return Mage_Sales_Model_Order_Payment
    $order->save();

    $ordersList[] = $order;
}

Mage::register('orders_list', $ordersList);
