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

class Mage_Admin_Model_Mysql4_Role extends Mage_Core_Model_Mysql4_Abstract
{
    protected function _construct()
    {
        $this->_init('admin/role', 'role_id');
    }

    /**
     *
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        if ( !$object->getId() ) {
            $object->setCreated(now());
        }
        $object->setModified(now());
        return $this;
    }

    public function load(Mage_Core_Model_Abstract $object, $value, $field=null)
    {
        if (!intval($value) && is_string($value)) {
            $field = 'role_id';
        }
        return parent::load($object, $value, $field);
    }
}
