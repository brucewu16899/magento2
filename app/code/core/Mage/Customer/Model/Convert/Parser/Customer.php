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
 * @category   Mage
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Customer_Model_Convert_Parser_Customer extends Mage_Eav_Model_Convert_Parser_Abstract
{
    public function parse()
    {
        return $this;
    }

    public function unparse()
    {
        return $this;
    }

    public function getExternalAttributes()
    {
        $internal = array('store_id', 'created_in', 'default_billing', 'default_shipping', 'country_id', 'group_id');

        $entityTypeId = Mage::getSingleton('eav/config')->getEntityType('customer')->getId();
        $customerAttributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entityTypeId)
            ->load()->getIterator();

        $entityTypeId = Mage::getSingleton('eav/config')->getEntityType('customer_address')->getId();
        $addressAttributes = Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entityTypeId)
            ->load()->getIterator();

        $attributes = array(
            'store'=>'store',
            'entity_id'=>'entity_id',
            'group'=>'group',
        );

        foreach ($customerAttributes as $attr) {
            $code = $attr->getAttributeCode();
            if (in_array($code, $internal) || $attr->getFrontendInput()=='hidden') {
                continue;
            }
            $attributes[$code] = $code;
        }
        $attributes['password_hash'] = 'password_hash';

        foreach ($addressAttributes as $attr) {
            $code = $attr->getAttributeCode();
            if (in_array($code, $internal) || $attr->getFrontendInput()=='hidden') {
                continue;
            }
            $attributes['billing_'.$code] = 'billing_'.$code;
        }
        $attributes['billing_country'] = 'billing_country';

        foreach ($addressAttributes as $attr) {
            $code = $attr->getAttributeCode();
            if (in_array($code, $internal) || $attr->getFrontendInput()=='hidden') {
                continue;
            }
            $attributes['shipping_'.$code] = 'shipping_'.$code;
        }
        $attributes['shipping_country'] = 'shipping_country';

        return $attributes;
    }
}