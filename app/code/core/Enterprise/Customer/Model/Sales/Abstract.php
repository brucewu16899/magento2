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
 * @package     Enterprise_Customer
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Customer abstract model
 *
 */
abstract class Enterprise_Customer_Model_Sales_Abstract extends Enterprise_Enterprise_Model_Core_Abstract
{
    /**
     * Resource wrapper
     * 
     * @return Enterprise_Customer_Model_Mysql4_Sales_Abstract
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Save new attribute
     *
     * @param Mage_Customer_Model_Attribute $attribute
     * @return Enterprise_Customer_Model_Sales_Abstract
     */
    public function saveNewAttribute(Mage_Customer_Model_Attribute $attribute)
    {
        $this->_getResource()->saveNewAttribute($attribute);
        return $this;
    }

    /**
     * Delete attribute
     *
     * @param Mage_Customer_Model_Attribute $attribute
     * @return Enterprise_Customer_Model_Sales_Abstract
     */
    public function deleteAttribute(Mage_Customer_Model_Attribute $attribute)
    {
        $this->_getResource()->deleteAttribute($attribute);
        return $this;
    }

    /**
     * Attach extended data to sales object
     *
     * @param Mage_Core_Model_Abstract $sales
     * @return Enterprise_Customer_Model_Sales_Abstract
     */
    public function attachAttributeData(Mage_Core_Model_Abstract $sales)
    {
        $sales->addData($this->getData());
        return $this;
    }

    /**
     * Save extended attributes data
     *
     * @param Mage_Core_Model_Abstract $sales
     * @return Enterprise_Customer_Model_Sales_Abstract
     */
    public function saveAttributeData(Mage_Core_Model_Abstract $sales)
    {
        $this->setId($sales->getId())
             ->addData($sales->getData())
             ->save();
        
        return $this;
    }

    /**
     * CopyFieldset converts customer attributes from source object to target object
     *
     * @param Mage_Core_Model_Abstract $source
     * @param Mage_Core_Model_Abstract $target
     * @param array $fields
     * @param bool $useColumnPrefix
     * @return Enterprise_Customer_Model_Sales_Abstract
     */
    public function copyFieldsetSourceToTarget(
        Mage_Core_Model_Abstract $source,
        Mage_Core_Model_Abstract $target,
        array                    $fields = null,
                                 $useColumnPrefix = false
    ){
        $this->_getResource()->copyFieldsetSourceToTarget($source, $target, $fields, $useColumnPrefix);
        return $this;
    }

    /**
     * Describe table
     *
     * @return array
     */
    public function describeTable(){
        return $this->_getResource()->describeTable();
    }
}
