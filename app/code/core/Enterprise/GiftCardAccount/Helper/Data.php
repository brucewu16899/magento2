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
 * @category   Enterprise
 * @package    Enterprise_GiftCardAccount
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Enterprise_GiftCardAccount_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Unserialize and return gift card list from specified object
     *
     * @param Varien_Object $from
     * @return mixed
     */
    public function getCards(Varien_Object $from)
    {
        $value = $from->getGiftCards();
        if (!$value) {
            return array();
        }
        return unserialize($value);
    }

    /**
     * Serialize and set gift card list to specified object
     *
     * @param Varien_Object $to
     * @param mixed $value
     */
    public function setCards(Varien_Object $to, $value)
    {
        $serializedValue = serialize($value);
        $to->setGiftCards($serializedValue);
    }
}