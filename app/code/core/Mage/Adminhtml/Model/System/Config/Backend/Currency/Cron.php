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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Backend Model for Currency import options
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Model_System_Config_Backend_Currency_Cron
{
    const CRON_STRING_PATH = 'crontab/jobs/currency_rates_update/schedule/cron_expr';

    public function afterSave(Varien_Object $configData)
    {
        $enabled = $configData->getData('groups/import/fields/enabled/value');
        $service = $configData->getData('groups/import/fields/service/value');
        $time = $configData->getData('groups/import/fields/time/value');
        $frequncy = $configData->getData('groups/import/fields/frequncy/value');
        $errorEmail = $configData->getData('groups/import/fields/error_email/value');

        $frequencyDayly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_DAYLY ;
        $frequencyWeekly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_WEEKLY;
        $frequencyMonthly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_MONTHLY;
        /*$frequencyYearly = Mage_Adminhtml_Model_System_Config_Source_Cron_Frequency::CRON_YEARLY;*/

        $cronDayOfWeek = date('N');

        $cronExprArray = array(
            intval($time[1]),    # Minute
            intval($time[0]),    # Hour
            ( $frequncy == $frequencyMonthly ) ? '1' : '*',    # Day of the Month
            '*',    # Month of the Year
            ( $frequncy == $frequencyDayly ) ? '1' : '*',    # Day of the Week
            /*( $frequncy == $frequencyYearly ) ? '1' : '*',     # Year*/
        );

        $cronExprString = join(' ', $cronExprArray);

        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->save();
        } catch (Exception $e) {
            throw new Exception(Mage::getHelper('adminhtml')->__('Unable to save Cron expression'));
        }
    }
}