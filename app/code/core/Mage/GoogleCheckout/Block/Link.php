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
 * @package    Mage_GoogleCheckout
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Google Checkout shortcut link
 *
 * @category   Mage
 * @package    Mage_GoogleCheckout
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Mage_GoogleCheckout_Block_Link extends Mage_Core_Block_Text_List_Link
{
    public function getAParams()
    {
        return array(
            'href'=>$this->getUrl('googlecheckout/start', array('_secure'=>true))
        );
    }

    public function getInnerText()
    {
        return '<img src="'.$this->getSkinUrl('images/btn_google_checkout.gif').'" alt="'.__('Google Checkout').'"/>';
    }

    public function _beforeToHtml()
    {
        return (bool)Mage::getStoreConfig('payment/googlecheckout/active');
    }
}