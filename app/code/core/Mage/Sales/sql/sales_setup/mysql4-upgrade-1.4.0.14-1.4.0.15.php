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
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$profileTable = $this->getTable('sales_recurring_profile');
$customerEntityTable = $this->getTable('customer_entity');
$coreStoreTable = $this->getTable('core_store');
$profileOrderTable = $this->getTable('sales_recurring_profile_order');
$orderTable = $this->getTable('sales_flat_order');

$this->run("DROP TABLE IF EXISTS `{$profileOrderTable}`");
$this->run("DROP TABLE IF EXISTS `{$profileTable}`");

$this->run("
CREATE TABLE `{$profileTable}` (
  `profile_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `state` varchar(20) NOT NULL,
  `customer_id` int(10) unsigned DEFAULT NULL,
  `store_id` smallint(5) unsigned DEFAULT NULL,
  `method_code` varchar(32) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `reference_id` varchar(32) DEFAULT NULL,
  `subscriber_name` varchar(150) DEFAULT NULL,
  `start_datetime` datetime NOT NULL,
  `internal_reference_id` varchar(42) NOT NULL,
  `schedule_description` varchar(255) NOT NULL,
  `suspension_threshold` smallint(6) unsigned DEFAULT NULL,
  `bill_failed_later` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `period_unit` varchar(20) NOT NULL,
  `period_frequency` tinyint(3) unsigned DEFAULT NULL,
  `period_max_cycles` tinyint(3) unsigned DEFAULT NULL,
  `billing_amount` double(12,4) unsigned NOT NULL DEFAULT '0.0000',
  `trial_period_unit` varchar(20) DEFAULT NULL,
  `trial_period_frequency` tinyint(3) unsigned DEFAULT NULL,
  `trial_period_max_cycles` tinyint(3) unsigned DEFAULT NULL,
  `trial_billing_amount` double(12,4) unsigned DEFAULT NULL,
  `currency_code` char(3) NOT NULL,
  `shipping_amount` decimal(12,4) unsigned DEFAULT NULL,
  `tax_amount` decimal(12,4) unsigned DEFAULT NULL,
  `init_amount` decimal(12,4) unsigned DEFAULT NULL,
  `init_may_fail` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `order_info` text NOT NULL,
  `order_item_info` text NOT NULL,
  `billing_address_info` text NOT NULL,
  `shipping_address_info` text DEFAULT NULL,
  `profile_vendor_info` text DEFAULT NULL,
  `additional_info` text DEFAULT NULL,
  PRIMARY KEY (`profile_id`),
  UNIQUE KEY `UNQ_INTERNAL_REF_ID` (`internal_reference_id`),
  KEY `IDX_RECURRING_PROFILE_CUSTOMER` (`customer_id`),
  KEY `IDX_RECURRING_PROFILE_STORE` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

$this->getConnection()->addConstraint('FK_RECURRING_PROFILE_CUSTOMER', $profileTable, 'customer_id',
    $customerEntityTable, 'entity_id', 'SET NULL'
);

$this->getConnection()->addConstraint('FK_RECURRING_PROFILE_STORE', $profileTable, 'store_id',
    $customerEntityTable, 'store_id', 'SET NULL'
);

$this->run("
CREATE TABLE `{$profileOrderTable}` (
  `link_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `profile_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`link_id`),
  UNIQUE KEY `UNQ_PROFILE_ORDER` (`profile_id`,`order_id`),
  KEY `IDX_ORDER` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$this->getConnection()->addConstraint('FK_RECURRING_PROFILE_ORDER_PROFILE', $profileOrderTable, 'profile_id',
    $profileTable, 'profile_id'
);

$this->getConnection()->addConstraint('FK_RECURRING_PROFILE_ORDER_ORDER', $profileOrderTable, 'order_id',
    $orderTable, 'entity_id'
);
