<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

//this fixture creates simple Catalog Rule
// Active, applied to all products, without time limits, with 10% off for Not Logged In Customers

/** @var $banner Mage_CatalogRule_Model_Rule */
$catalogRule = Mage::getModel('Mage_CatalogRule_Model_Rule');

$catalogRule->setIsActive(1)
    ->setName('Test Catalog Rule')
    ->setCustomerGroupIds(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID)
    ->setDiscountAmount(10)
    ->setWebsiteIds(array(0 => 1))
    ->setSimpleAction('by_percent')
    ->save();

$catalogRule->applyAll();
