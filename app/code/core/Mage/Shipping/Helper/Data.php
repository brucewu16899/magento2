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
 * @package    Mage_Shipping
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Shipping data helper
 */
class Mage_Shipping_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getTrackingAjaxUrl()
    {
        return $this->_getUrl('shipping/tracking/ajax');
    }
    public function isShipped($order)
    {
        if ($order->hasShippments() && $order->getTrackingNumbers()){
            return true;
        }
        return false;
    }
    public function getTrackingNumbers($order)
    { 
        $trackingNumbers = '';
        foreach ($order->getTrackingNumbers() as $trackingNumber) {
            $trackingNumbers = "'".$trackingNumber."', ";
        }
        return $trackingNumbers;
    }
}
