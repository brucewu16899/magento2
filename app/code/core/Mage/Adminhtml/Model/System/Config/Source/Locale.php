<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
/**
 * Locale source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Model_System_Config_Source_Locale
{
    public function toOptionArray()
    {
        return Mage::app()->getLocale()->getOptionLocales();
    }
}
