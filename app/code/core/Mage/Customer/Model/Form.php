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
 * @copyright   Copyright (c) 2010 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer Form Model
 *
 * @category    Mage
 * @package     Mage_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Customer_Model_Form
{
    /**
     * Current store instance
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * Current entity type instance
     *
     * @var Mage_Eav_Model_Entity_Type
     */
    protected $_entityType;

    /**
     * Current entity instance
     *
     * @var Mage_Core_Model_Abstract
     */
    protected $_entity;

    /**
     * Current form code
     *
     * @var string
     */
    protected $_formCode;

    /**
     * Array of form attributes
     *
     * @var array
     */
    protected $_attributes;

    /**
     * Array of form system attributes
     *
     * @var array
     */
    protected $_systemAttributes;

    /**
     * Array of form user defined attributes
     *
     * @var array
     */
    protected $_userAttributes;

    /**
     * Array of attribute data models by input type
     *
     * @var array
     */
    protected $_attributeDataModels     = array();

    /**
     * Is AJAX request flag
     *
     * @var boolean
     */
    protected $_isAjax                  = false;

    /**
     * Set current store
     *
     * @param Mage_Core_Model_Store|string|int $store
     * @return Mage_Customer_Model_Form
     */
    public function setStore($store)
    {
        $this->_store = Mage::app()->getStore($store);
        return $this;
    }

    /**
     * Set entity instance
     *
     * @param Mage_Core_Model_Abstract $entity
     * @return Mage_Customer_Model_Form
     */
    public function setEntity(Mage_Core_Model_Abstract $entity)
    {
        $this->_entity = $entity;
        if ($entity->getEntityTypeId()) {
            $this->setEntityType($entity->getEntityTypeId());
        }
        return $this;
    }

    /**
     * Set entity type instance
     *
     * @param Mage_Eav_Model_Entity_Type|string|int $entityType
     * @return Mage_Customer_Model_Form
     */
    public function setEntityType($entityType)
    {
        $this->_entityType = Mage::getSingleton('eav/config')->getEntityType($entityType);
        return $this;
    }

    /**
     * Set form code
     *
     * @param string $formCode
     * @return Mage_Customer_Model_Form
     */
    public function setFormCode($formCode)
    {
        $this->_formCode = $formCode;
        return $this;
    }

    /**
     * Return current store instance
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        if (is_null($this->_store)) {
            $this->_store = Mage::app()->getStore();
        }
        return $this->_store;
    }

    /**
     * Return current form code
     *
     * @throws Mage_Core_Exception
     * @return string
     */
    public function getFormCode()
    {
        if (empty($this->_formCode)) {
            Mage::throwException(Mage::helper('customer')->__('Form code is not defined'));
        }
        return $this->_formCode;
    }

    /**
     * Return entity type instance
     * Return customer entity type if entity type is not defined
     *
     * @return Mage_Eav_Model_Entity_Type
     */
    public function getEntityType()
    {
        if (is_null($this->_entityType)) {
            $this->setEntityType('customer');
        }
        return $this->_entityType;
    }

    /**
     * Return current entity instance
     *
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Abstract
     */
    public function getEntity()
    {
        if (is_null($this->_entity)) {
            Mage::throwException(Mage::helper('customer')->__('Entity instance is not defined'));
        }
        return $this->_entity;
    }

    /**
     * Return array of form attributes
     *
     * @return array
     */
    public function getAttributes()
    {
        if (is_null($this->_attributes)) {
            /* @var $collection Mage_Customer_Model_Entity_Form_Attribute_Collection */
            $collection = Mage::getResourceModel('customer/form_attribute_collection');
            $collection->setStore($this->getStore())
                ->setEntityType($this->getEntityType())
                ->addFormCodeFilter($this->getFormCode())
                ->setSortOrder();
            $this->_attributes      = array();
            $this->_userAttributes  = array();
            foreach ($collection as $attribute) {
                /* @var $attribute Mage_Eav_Model_Entity_Attribute */
                $this->_attributes[$attribute->getAttributeCode()] = $attribute;
                if ($attribute->getIsUserDefined()) {
                    $this->_userAttributes[$attribute->getAttributeCode()] = $attribute;
                } else {
                    $this->_systemAttributes[$attribute->getAttributeCode()] = $attribute;
                }
            }
        }

        return $this->_attributes;
    }

    /**
     * Return attribute instance by code or false
     *
     * @param string $attributeCode
     * @return Mage_Eav_Model_Entity_Attribute|false
     */
    public function getAttribute($attributeCode)
    {
        $attributes = $this->getAttributes();
        if (isset($attributes[$attributeCode])) {
            return $attributes[$attributeCode];
        }
        return false;
    }

    /**
     * Return array of form user defined attributes
     *
     * @return array
     */
    public function getUserAttributes()
    {
        if (is_null($this->_userAttributes)) {
            // load attributes
            $this->getAttributes();
        }
        return $this->_userAttributes;
    }

    /**
     * Return array of form system attributes
     *
     * @return array
     */
    public function getSystemAttributes()
    {
        if (is_null($this->_userAttributes)) {
            // load attributes
            $this->getAttributes();
        }
        return $this->_systemAttributes;
    }

    /**
     * Return attribute data model by attribute
     *
     * @param Mage_Eav_Model_Entity_Attribute $attribute
     * @return Mage_Customer_Model_Attribute_Data_Abstract
     */
    protected function _getAttributeDataModel(Mage_Eav_Model_Entity_Attribute $attribute)
    {
        /* @var $dataModel Mage_Customer_Model_Attribute_Data_Abstract */
        if (empty($this->_attributeDataModels[$attribute->getFrontendInput()])) {
            $dataModelClass = sprintf('customer/attribute_data_%s', $attribute->getFrontendInput());
            $dataModel      = Mage::getModel($dataModelClass);
            $this->_attributeDataModels[$attribute->getFrontendInput()] = $dataModel;
        } else {
            $dataModel = $this->_attributeDataModels[$attribute->getFrontendInput()];
        }
        $dataModel->setAttribute($attribute);
        $dataModel->setEntity($this->getEntity());
        $dataModel->setIsAjaxRequest($this->getIsAjaxRequest());

        return $dataModel;
    }

    /**
     * Prepare request with data and returns it
     *
     * @param array $data
     * @return Zend_Controller_Request_Http
     */
    public function prepareRequest(array $data)
    {
        $request = clone Mage::app()->getRequest();
        $request->setParamSources();
        $request->clearParams();
        $request->setParams($data);

        return $request;
    }

    /**
     * Extract data from request and return associative data array
     *
     * @param Zend_Controller_Request_Http $request
     * @param string $scope the request scope
     * @return array
     */
    public function extractData(Zend_Controller_Request_Http $request, $scope = null)
    {
        $data = array();
        foreach ($this->getAttributes() as $attribute) {
            $dataModel = $this->_getAttributeDataModel($attribute);
            $dataModel->setRequestScope($scope);
            $data[$attribute->getAttributeCode()] = $dataModel->extractValue($request);
        }
        return $data;
    }

    /**
     * Validate data array and return true or array of errors
     *
     * @param array $data
     * @return boolean|array
     */
    public function validateData(array $data)
    {
        $errors = array();
        foreach ($this->getAttributes() as $attribute) {
            $dataModel = $this->_getAttributeDataModel($attribute);
            if (!isset($data[$attribute->getAttributeCode()])) {
                $data[$attribute->getAttributeCode()] = null;
            }
            $result = $dataModel->validateValue($data[$attribute->getAttributeCode()]);
            if ($result !== true) {
                $errors = array_merge($errors, $result);
            }
        }

        if (count($errors) == 0) {
            return true;
        }

        return $errors;
    }

    /**
     * Compact data array to current entity
     *
     * @param array $data
     * @return Mage_Customer_Model_Form
     */
    public function compactData(array $data)
    {
        foreach ($this->getAttributes() as $attribute) {
            $dataModel = $this->_getAttributeDataModel($attribute);
            if (!isset($data[$attribute->getAttributeCode()])) {
                $data[$attribute->getAttributeCode()] = false;
            }
            $dataModel->compactValue($data[$attribute->getAttributeCode()]);
        }

        return $this;
    }

    /**
     * Restore data array from SESSION to current entity
     *
     * @param array $data
     * @return Mage_Customer_Model_Form
     */
    public function restoreData(array $data)
    {
        foreach ($this->getAttributes() as $attribute) {
            $dataModel = $this->_getAttributeDataModel($attribute);
            if (!isset($data[$attribute->getAttributeCode()])) {
                $data[$attribute->getAttributeCode()] = false;
            }
            $dataModel->restoreValue($data[$attribute->getAttributeCode()]);
        }
        return $this;
    }

    /**
     * Return array of entity formated values
     *
     * @return array
     */
    public function outputData()
    {
        $data = array();
        foreach ($this->getAttributes() as $attribute) {
            $dataModel = $this->_getAttributeDataModel($attribute);
            $data[$attribute->getAttributeCode()] = $dataModel->outputValue();
        }
        return $data;
    }

    /**
     * Set is AJAX Request flag
     *
     * @param boolean $flag
     * @return Mage_Customer_Model_Form
     */
    public function setIsAjaxRequest($flag = true)
    {
        $this->_isAjax = (bool)$flag;
        return $this;
    }

    /**
     * Return is AJAX Request
     *
     * @return boolean
     */
    public function getIsAjaxRequest()
    {
        return $this->_isAjax;
    }

    /**
     * Set default attribute values for new entity
     *
     * @return Mage_Customer_Model_Form
     */
    public function initDefaultValues()
    {
        if (!$this->getEntity()->getId()) {
            foreach ($this->getAttributes() as $attribute) {
                $default = $attribute->getDefaultValue();
                if ($default) {
                    $this->getEntity()->setData($attribute->getAttributeCode(), $default);
                }
            }
        }
        return $this;
    }
}
