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
 * @package    Mage_Permissions
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Permissions_Model_Mysql4_Roles_User_Collection extends Varien_Data_Collection_Db
{
	protected $_roleTable;

    public function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('tag_read'));

        $this->_roleTable = Mage::getSingleton('core/resource')->getTableName('permissions/admin_role');
        $this->_userTable = Mage::getSingleton('core/resource')->getTableName('permissions/admin_user');
        /*$this->_sqlSelect->from($this->_roleTable, '*');
        $this->_sqlSelect->where("{$this->_roleTable}.role_type='U'");*/
        ///////////////////
        $this->_sqlSelect->from($this->_roleTable, '*')
                            ->joinLeft(array('usr' => $this->_userTable), "(usr.user_id = `{$this->_roleTable}`.user_id)", array('*'));
        $this->_sqlSelect->where("{$this->_roleTable}.role_type='U'");
    }

    public function setRoleFilter($roleId)
    {
        $this->_sqlSelect->where("{$this->_roleTable}.parent_id = ?", $roleId);
        return $this;
    }

    public function setUserFilter($userId)
    {
        $this->_sqlSelect->where("{$this->_roleTable}.user_id = ?", $userId);
        return $this;
    }
}
?>