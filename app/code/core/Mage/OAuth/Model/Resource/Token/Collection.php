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
 * @package     Mage_OAuth
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * OAuth token resource collection model
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_Model_Resource_Token_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize collection model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('oauth/token');
    }

    /**
     * Load collection with consumer data
     *
     * Method use for show applications list (token-consumer)
     *
     * @return Mage_OAuth_Model_Resource_Token_Collection
     */
    public function joinConsumerAsApplication()
    {
        $select = $this->getSelect();
        $select->reset(Zend_Db_Select::COLUMNS)
                ->columns(array('entity_id', 'consumer_id', 'is_revoked'), 'main_table')
                ->joinLeft(
                    array('c' => $this->getTable('oauth/consumer')),
                    'c.entity_id = main_table.consumer_id',
                    'name'
                );

        return $this;
    }

    /**
     * Add filter by admin ID
     *
     * @param int $adminId
     * @return Mage_OAuth_Model_Resource_Token_Collection
     */
    public function addFilterByAdminId($adminId)
    {
        $this->addFilter('main_table.admin_id', $adminId);
        return $this;
    }

    /**
     * Add filter by ID
     *
     * @param array|int $id
     * @return Mage_OAuth_Model_Resource_Token_Collection
     */
    public function addFilterById($id)
    {
        $this->addFilter('main_table.entity_id', array('in' => $id), 'public');
        return $this;
    }

    /**
     * Add filter by "Is Revoked" status
     *
     * @param bool|int $flag
     * @return Mage_OAuth_Model_Resource_Token_Collection
     */
    public function addFilterByIsRevoked($flag)
    {
        $this->addFilter('main_table.is_revoked', (int) $flag, 'public');
        return $this;
    }
}
