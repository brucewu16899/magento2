<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_DesignEditor
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
$utility = Mage_Admin_Utility_User::getInstance();
$utility->destroyAdmin();

$session = new Mage_DesignEditor_Model_Session();
$session->deactivateDesignEditor();