<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */
class Enterprise_CustomerSegment_Model_Segment extends Mage_Rule_Model_Rule
{
    /**
     * Intialize model
     *
     * @return void
     */    
    protected function _construct()
    {
        parent::_construct();
        $this->_init('enterprise_customersegment/segment');
        $this->setIdFieldName('segment_id');
    }

    /**
     * Return conditions instance
     *
     * @return Enterprise_CustomerSegment_Model_Segment_Condition_Combine
     */    
    public function getConditionsInstance()
    {
        return Mage::getModel('enterprise_customersegment/segment_condition_combine_root');
    }

    /**
     * Perform actions after object load
     */
    protected function _afterLoad()
    {
        Mage_Core_Model_Abstract::_afterLoad();
        $conditionsArr = unserialize($this->getConditionsSerialized());
        if (!empty($conditionsArr) && is_array($conditionsArr)) {
            $this->getConditions()->loadArray($conditionsArr);
        }
        return $this;
    }

    /**
     * Perform actions before object save
     */
    protected function _beforeSave()
    {
        if (!$this->getData('processing_frequency')){
            $this->setData('processing_frequency', '1');
        }

        $events = array();
        if ($this->getIsActive()) {
            foreach ($this->getConditionModels() as $model) {
                $eventName = Mage::getModel($model)->getValidationEvent();
                if (is_null($eventName)) {
                    continue;
                }

                if (!is_array($eventName)) {
                    $eventName = array($eventName);
                }

                $events = array_merge($events, $eventName);
            }
        }
        $this->setValidationEvents(array_unique($events));

        parent::_beforeSave();
    }
 

    public function getConditionModels($conditions = null)
    {
        $result = array();

        if (is_null($conditions)) {
            $conditions = $this->getConditions();
        }

        $result[] = $conditions->getType();
        $childConditions = $conditions->getConditions();
        if ($childConditions) {
            if (is_array($childConditions)) {
                foreach ($childConditions as $child) {
                    $result = array_merge($result, $this->getConditionModels($child));
                }
            } else {
                $result = array_merge($result, $this->getConditionModels($childConditions));
            }
        }

        return $result;
    }

    public function validate($object, $website = null)
    {
        $sql = $this->getConditions()->getConditionsSql($object, $website);
        echo "$sql\n<br />\n";

        $result = $this->getResource()->runConditionSql($sql);

        $resultText = ($result ? '<span style="color: #00CC00;">PASSED</span>' : '<span style="color: #CC0000;">FAILED</span>');
        echo "SEGMENT #{$segment->getId()} VALIDATION AGAINST CUSTOMER #{$customer->getId()} {$resultText}\n<br /><br />\n";

        return $result;
    }

    public function getMatchedSegmentsByCustomer($customer = null)
    {
        if (is_null($customer)) {
            Mage::getSingleton('customer/session')->getCustomerId();
            // load from session instead of db
        } else if ($customer instanceof Mage_Customer_Model_Customer) {
            $customer = $customer->getId();
        } else if (is_array($customer)) {
            // use customer array [return matches]
        } else if ($customer instanceof Zend_Db_Select) {
            $customer = new Zend_Db_Expr($customer);
        }

        // load customers
        return array();
    }
}
