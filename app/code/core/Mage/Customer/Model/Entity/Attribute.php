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
 * @package     Mage_Customer
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer attribute resource model
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Entity_Attribute extends Mage_Eav_Model_Mysql4_Entity_Attribute
{
    /**
     * Perform actions before object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Customer_Model_Entity_Attribute
     */
    protected function _beforeSave(Mage_Core_Model_Abstract $object)
    {
        $validateRules = $object->getData('validate_rules');
        if (is_array($validateRules)) {
            $object->setData('validate_rules', serialize($validateRules));
        }
        return parent::_beforeSave($object);
    }

    /**
     * Save attribute/form relations after attribute save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Customer_Model_Entity_Attribute
     */
    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        $forms = $object->getData('used_in_forms');
        if (is_array($forms)) {
            $where = array('attribute_id=?' => $object->getId());
            $this->_getWriteAdapter()->delete($this->getTable('customer/form_attribute'), $where);

            $data = array();
            foreach ($forms as $formCode) {
                $data[] = array(
                    'form_code'     => $formCode,
                    'attribute_id'  => intval($object->getId())
                );
            }

            if ($data) {
                $this->_getWriteAdapter()->insertMultiple($this->getTable('customer/form_attribute'), $data);
            }
        }

        // update sort order
        if (!$object->isObjectNew() && $object->dataHasChangedFor('sort_order')) {
            $bind = array(
                'sort_order' => $object->getSortOrder()
            );
            $where = $this->_getWriteAdapter()->quoteInto('attribute_id=?', $object->getId());
            $this->_getWriteAdapter()->update($this->getTable('eav/entity_attribute'), $bind, $where);
        }

        return parent::_afterSave($object);
    }

    /**
     * Return forms in which the attribute
     *
     * @param Mage_Core_Model_Abstract $object
     * @return array
     */
    public function getUsedInForms(Mage_Core_Model_Abstract $object)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('customer/form_attribute'), 'form_code')
            ->where('attribute_id = ?', (int)$object->getId());
        return $this->_getReadAdapter()->fetchCol($select);
    }
}
