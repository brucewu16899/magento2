<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Report Reviews collection
 *
 * @category    Mage
 * @package     Mage_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Invitation_Model_Resource_Report_Invitation_Customer_Initial
        extends Mage_Reports_Model_Resource_Report_Collection
{
    /**
     *  Report subcollection class name
     *
     * @var Enterprise_Invitation_Model_Resource_Report_Invitation_Customer_Collection $_reportCollectionClass
     */
    protected $_reportCollectionClass = 'Enterprise_Invitation_Model_Resource_Report_Invitation_Customer_Collection';
}