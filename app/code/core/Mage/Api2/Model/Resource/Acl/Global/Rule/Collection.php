<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Api2 global ACL rule resource collection model
 *
 * @category    Mage
 * @package     Mage_Api2
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Resource_Acl_Global_Rule_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize collection model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mage_Api2_Model_Acl_Global_Rule', 'Mage_Api2_Model_Resource_Acl_Global_Rule');
    }

    /**
     * Add filtering by role ID
     *
     * @param int $roleId
     * @return Mage_Api2_Model_Resource_Acl_Global_Rule_Collection
     */
    public function addFilterByRoleId($roleId)
    {
        $this->addFilter('role_id', $roleId, 'public');
        return $this;
    }
}