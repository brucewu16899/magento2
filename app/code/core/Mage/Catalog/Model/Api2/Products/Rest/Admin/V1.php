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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 for products collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Products_Rest_Admin_V1 extends Mage_Catalog_Model_Api2_Products_Rest
{
    /**
     * The greatest decimal value which could be stored. Corresponds to DECIMAL (12,4) SQL type
     */
    const MAX_DECIMAL_VALUE = 99999999.9999;

    /**
     * Create product
     *
     * @param array $data
     * @return string
     */
    protected function _create(array $data)
    {
        $this->_validate($data);

        $type = $data['type'];
        $set = $data['set'];
        $sku = $data['sku'];

        $store = isset($data['store']) ? $data['store'] : '';
        $storeId = Mage::app()->getStore($store)->getId();
        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::getModel('catalog/product')
            ->setStoreId($storeId)
            ->setAttributeSetId($set)
            ->setTypeId($type)
            ->setSku($sku);

        $this->_prepareDataForSave($product, $data);
        try {
            $product->validate();
            $product->save();
            $this->_multicall($product->getId());
        } catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $this->_critical(sprintf('Invalid attribute "%s": %s', $e->getAttributeCode(), $e->getMessage()),
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
        }

        return $this->_getLocation($product);
    }

    /**
     * Pre-validate request data
     *
     * @param array $data
     * @param array $required
     * @param array $notEmpty
     */
    protected function _validate(array $data, array $required = array(), array $notEmpty = array())
    {
        parent::_validate($data, $required, $notEmpty);
        $this->_validateProductType($data);
        /** @var $productEntity Mage_Eav_Model_Entity_Type */
        $productEntity = Mage::getModel('eav/entity_type')->loadByCode(Mage_Catalog_Model_Product::ENTITY);
        $this->_validateAttributeSet($data, $productEntity);
        $this->_validateStore($data);
        $this->_validateSku($data);
        $this->_validateGroupPrice($data);
        $this->_validateTierPrice($data);
        $this->_validateStockData($data);
        $requiredAttributes = $this->_validateAttributes($data, $productEntity);

        parent::_validate($data, $requiredAttributes, $requiredAttributes);
    }

    /**
     * Collect required EAV attributes, validate applicable attributes and validate source attributes values
     *
     * @param array $data
     * @param Mage_Eav_Model_Entity_Type $productEntity
     * @return array
     */
    protected function _validateAttributes($data, $productEntity)
    {
        if (!isset($data['set']) || empty($data['set'])) {
            $this->_critical('Missing "set" in request.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        if (!isset($data['type']) || empty($data['type'])) {
            $this->_critical('Missing "type" in request.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        // Validate weight
        if (isset($data['weight']) && !empty($data['weight']) && $data['weight'] > 0
            && !Zend_Validate::is($data['weight'], 'Between', array(0, self::MAX_DECIMAL_VALUE))) {
            $this->_error('The "weight" value is not within the specified range.',
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        // msrp_display_actual_price_type attribute values needs to be a string to pass validation
        // see Mage_Catalog_Model_Product_Attribute_Source_Msrp_Type_Price::getAllOptions()
        if (isset($data['msrp_display_actual_price_type'])) {
            $data['msrp_display_actual_price_type'] = (string) $data['msrp_display_actual_price_type'];
        }
        $requiredAttributes = array('set');
        $positiveNumberAttributes = array('weight', 'price', 'special_price', 'msrp');
        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        foreach ($productEntity->getAttributeCollection($data['set']) as $attribute) {
            $attributeCode = $attribute->getAttributeCode();
            $value = false;
            $isSet = false;
            if (isset($data[$attribute->getAttributeCode()])) {
                $value = $data[$attribute->getAttributeCode()];
                $isSet = true;
            }
            $applicable = false;
            if (!$attribute->getApplyTo() || in_array($data['type'], $attribute->getApplyTo())) {
                $applicable = true;
            }

            if (!$applicable && !$attribute->isStatic() && $isSet) {
                $productTypes = Mage_Catalog_Model_Product_Type::getTypes();
                $this->_error(sprintf('Attribute "%s" is not applicable for product type "%s"', $attributeCode,
                    $productTypes[$data['type']]['label']), Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }

            if ($applicable && $isSet) {
                // Validate dropdown attributes
                if ($attribute->usesSource() && !empty($value)) {
                    $allowedValues = $this->_getAttributeAllowedValues($attribute->getSource()->getAllOptions());
                    if (!in_array($value, $allowedValues, true)) {
                        $this->_error(sprintf('Invalid value for attribute "%s".', $attributeCode),
                            Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
                    }
                }
                // Validate datetime attributes
                if ($attribute->getBackendType() == 'datetime') {
                    try {
                        $attribute->getBackend()->formatDate($value);
                    } catch (Zend_Date_Exception $e) {
                        $this->_error(sprintf('Invalid date in the "%s" field.', $attributeCode),
                            Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
                    }
                }
                // Validate positive number required attributes
                if (in_array($attributeCode, $positiveNumberAttributes) && (!empty($value) && $value !== 0)
                    && (!is_numeric($value) || $value < 0)
                ) {
                    $this->_error(sprintf('Please enter a number 0 or greater in the "%s" field.', $attributeCode),
                        Mage_Api2_Model_Server::HTTP_BAD_REQUEST
                    );
                }
            }

            if ($applicable && $attribute->getIsRequired() && $attribute->getIsVisible()) {
                if (!in_array($attributeCode, $positiveNumberAttributes) || $value !== 0) {
                    $requiredAttributes[] = $attribute->getAttributeCode();
                }
            }
        }

        return $requiredAttributes;
    }

    /**
     * Validate product type
     *
     * @param array $data
     */
    protected function _validateProductType($data)
    {
        if (!isset($data['type']) || empty($data['type'])) {
            $this->_critical('Missing "type" in request.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        if (!array_key_exists($data['type'], Mage_Catalog_Model_Product_Type::getTypes())) {
            $this->_critical('Invalid product type.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Validate attribute set
     *
     * @param array $data
     * @param Mage_Eav_Model_Entity_Type $productEntity
     */
    protected function _validateAttributeSet($data, $productEntity)
    {
        if (!isset($data['set']) || empty($data['set'])) {
            $this->_critical('Missing "set" in request.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        /** @var $attributeSet Mage_Eav_Model_Entity_Attribute_Set */
        $attributeSet = Mage::getModel('eav/entity_attribute_set')->load($data['set']);
        if (!$attributeSet->getId() || $productEntity->getEntityTypeId() != $attributeSet->getEntityTypeId()) {
            $this->_critical('Invalid attribute set.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Validate store
     *
     * @param array $data
     */
    protected function _validateStore($data)
    {
        if (isset($data['store'])) {
            try {
                Mage::app()->getStore($data['store']);
            } catch (Mage_Core_Model_Store_Exception $e) {
                $this->_critical('Invalid store.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * Validate SKU
     *
     * @param array $data
     */
    protected function _validateSku($data)
    {
        $sku = $data['sku'];
        if (!Zend_Validate::is($sku, 'StringLength', array('min' => 0, 'max' => 64))) {
            $this->_error('SKU length should be 64 characters maximum.', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
    }

    /**
     * Validate Group Price complex attribute
     *
     * @param array $data
     */
    protected function _validateGroupPrice($data)
    {
        if (isset($data['group_price']) && is_array($data['group_price'])) {
            $groupPrices = $data['group_price'];
            foreach ($groupPrices as $index => $groupPrice) {
                $fieldSet = 'group_price:' . $index;
                $this->_validateWebsiteId($groupPrice, $fieldSet);
                $this->_validateCustomerGroup($groupPrice, $fieldSet);
                $this->_validatePositiveNumber($groupPrice, $fieldSet, 'price', true, true);
            }
        }
    }

    /**
     * Validate Tier Price complex attribute
     *
     * @param array $data
     */
    protected function _validateTierPrice($data)
    {
        if (isset($data['tier_price']) && is_array($data['tier_price'])) {
            $tierPrices = $data['tier_price'];
            foreach ($tierPrices as $index => $tierPrice) {
                $fieldSet = 'tier_price:' . $index;
                $this->_validateWebsiteId($tierPrice, $fieldSet);
                $this->_validateCustomerGroup($tierPrice, $fieldSet);
                $this->_validatePositiveNumber($tierPrice, $fieldSet, 'price_qty');
                $this->_validatePositiveNumber($tierPrice, $fieldSet, 'price');
            }
        }
    }

    /**
     * Validate product inventory data
     *
     * @param array $data
     */
    protected function _validateStockData($data)
    {
        if (isset($data['stock_data']) && is_array($data['stock_data'])) {
            $stockData = $data['stock_data'];
            $fieldSet = 'stock_data';
            if (!(isset($stockData['use_config_manage_stock']) && $stockData['use_config_manage_stock'])) {
                $this->_validateBoolean($stockData, $fieldSet, 'manage_stock');
                $manageStock = isset($stockData['manage_stock']) && $stockData['manage_stock'];
            } else {
                $manageStock = Mage::getStoreConfig(
                    Mage_CatalogInventory_Model_Stock_Item::XML_PATH_ITEM . 'manage_stock');
            }
            if ($manageStock) {
                $this->_validateNumeric($stockData, $fieldSet, 'qty');
                $this->_validatePositiveNumber($stockData, $fieldSet, 'min_qty', false, true, true);
                $this->_validateNumeric($stockData, $fieldSet, 'notify_stock_qty', false, true);
                $this->_validateBoolean($stockData, $fieldSet, 'is_qty_decimal');
                if (isset($stockData['is_qty_decimal']) && (bool) $stockData['is_qty_decimal'] == true) {
                    $this->_validateBoolean($stockData, $fieldSet, 'is_decimal_divided');
                }
                $this->_validateBoolean($stockData, $fieldSet, 'enable_qty_increments');
                if (isset($stockData['enable_qty_increments']) && (bool) $stockData['enable_qty_increments'] == true) {
                    $this->_validatePositiveInteger($stockData, $fieldSet, 'qty_increments', false, true);
                }
                if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
                    $this->_validateSource($stockData, $fieldSet, 'backorders',
                        'cataloginventory/source_backorders', true);
                    $this->_validateSource($stockData, $fieldSet, 'is_in_stock', 'cataloginventory/source_stock');
                }
            }

            $this->_validatePositiveInteger($stockData, $fieldSet, 'min_sale_qty', false, true);
            $this->_validatePositiveInteger($stockData, $fieldSet, 'max_sale_qty', false, true);
        }
    }

    /**
     * Validate Website ID field
     *
     * @param string $fieldSet
     * @param array $data
     */
    protected function _validateWebsiteId($data, $fieldSet)
    {
        if (!isset($data['website_id'])) {
            $this->_error(sprintf('The "website_id" value in the "%s" set is a required field.', $fieldSet),
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        } else {
            $website = Mage::getModel('core/website')->load($data['website_id']);
            if (is_null($website->getId())) {
                $this->_error(sprintf('Invalid "website_id" value in the "%s" set.', $fieldSet),
                    Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * Validate Customer Group field
     *
     * @param string $fieldSet
     * @param array $data
     */
    protected function _validateCustomerGroup($data, $fieldSet)
    {
        if (!isset($data['cust_group'])) {
            $this->_error(sprintf('The "cust_group" value in the "%s" set is a required field.', $fieldSet),
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        } else {
            $customerGroup = Mage::getModel('customer/group')->load($data['cust_group']);
            if (is_null($customerGroup->getId())) {
                $this->_error(sprintf('Invalid "cust_group" value in the "%s" set', $fieldSet),
                    Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * Validate field to be positive number
     *
     * @param array $data
     * @param string $fieldSet
     * @param string $field
     * @param bool $required
     * @param bool $equalsZero
     * @param bool $skipIfConfigValueUsed
     */
    protected function _validatePositiveNumber($data, $fieldSet, $field, $required = true, $equalsZero = false,
        $skipIfConfigValueUsed = false)
    {
        // in case when 'Use Config Settings' is selected no validation needed
        if (!($skipIfConfigValueUsed && $this->_isConfigValueUsed($data, $field))) {
            if (!isset($data[$field]) && $required) {
                $this->_error(sprintf('The "%s" value in the "%s" set is a required field.', $field, $fieldSet),
                    Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }

            if (isset($data[$field])) {
                $isValid = $equalsZero ? $data[$field] >= 0 : $data[$field] > 0;
                if (!(is_numeric($data[$field]) && $isValid)) {
                    $message = $equalsZero
                        ? 'Please enter a number 0 or greater in the "%s" field in the "%s" set.'
                        : 'Please enter a number greater than 0 in the "%s" field in the "%s" set.';
                    $this->_error(sprintf($message, $field, $fieldSet), Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
                }
            }
        }
    }

    /**
     * Validate field to be a positive integer
     *
     * @param array $data
     * @param string $fieldSet
     * @param string $field
     * @param bool $required
     * @param bool $skipIfConfigValueUsed
     */
    protected function _validatePositiveInteger($data, $fieldSet, $field, $required = false,
        $skipIfConfigValueUsed = false)
    {
        // in case when 'Use Config Settings' is selected no validation needed
        if (!($skipIfConfigValueUsed && $this->_isConfigValueUsed($data, $field))) {
            if (!isset($data[$field]) && $required) {
                $this->_error(sprintf('The "%s" value in the "%s" set is a required field.',$field, $fieldSet),
                    Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }

            if (isset($data[$field]) && (!is_int($data[$field]) || $data[$field] < 0)) {
                $this->_error(sprintf('Please use numbers only in the "%s" field in the "%s" set. ' .
                    'Please avoid spaces or other characters such as dots or commas.', $field, $fieldSet),
                    Mage_Api2_Model_Server::HTTP_BAD_REQUEST
                );
            }
        }
    }

    /**
     * Validate field to be a number
     *
     * @param array $data
     * @param string $fieldSet
     * @param string $field
     * @param bool $required
     * @param bool $skipIfConfigValueUsed
     */
    protected function _validateNumeric($data, $fieldSet, $field, $required = false, $skipIfConfigValueUsed = false)
    {
        // in case when 'Use Config Settings' is selected no validation needed
        if (!($skipIfConfigValueUsed && $this->_isConfigValueUsed($data, $field))) {
            if (!isset($data[$field]) && $required) {
                $this->_error(sprintf('The "%s" value in the "%s" set is a required field.',$field, $fieldSet),
                    Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }

            if (isset($data[$field]) && !is_numeric($data[$field])) {
                $this->_error(
                    sprintf('Please enter a valid number in the "%s" field in the "%s" set.', $field, $fieldSet),
                    Mage_Api2_Model_Server::HTTP_BAD_REQUEST
                );
            }
        }
    }

    /**
     * Validate dropdown fields value
     *
     * @param array $data
     * @param string $fieldSet
     * @param string $field
     * @param string $sourceModelName
     * @param bool $skipIfConfigValueUsed
     */
    protected function _validateSource($data, $fieldSet, $field, $sourceModelName, $skipIfConfigValueUsed = false)
    {
        // in case when 'Use Config Settings' is selected no validation needed
        if (!($skipIfConfigValueUsed && $this->_isConfigValueUsed($data, $field))) {
            if (isset($data[$field])) {
                $sourceModel = Mage::getSingleton($sourceModelName);
                if ($sourceModel) {
                    $allowedValues = $this->_getAttributeAllowedValues($sourceModel->toOptionArray());
                    if (!in_array($data[$field], $allowedValues, true)) {
                        $this->_error(sprintf('Invalid "%s" value in the "%s" set.', $field, $fieldSet),
                            Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
                    }
                }
            }
        }
    }

    /**
     * Validate bolean fields value
     *
     * @param array $data
     * @param string $fieldSet
     * @param string $field
     */
    protected function _validateBoolean($data, $fieldSet, $field)
    {
        if (isset($data[$field])) {
            $allowedValues = $this->_getAttributeAllowedValues(
                Mage::getSingleton('eav/entity_attribute_source_boolean')->getAllOptions());
            if (!in_array($data[$field], $allowedValues, true)) {
                $this->_error(sprintf('Invalid "%s" value in the "%s" set.', $field, $fieldSet),
                    Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
            }
        }
    }

    /**
     * Retrieve all attribute allowed values from source model in plain array format
     *
     * @param array $options
     * @return array
     */
    protected function _getAttributeAllowedValues(array $options)
    {
        $values = array();
        foreach ($options as $option) {
            if (isset($option['value'])) {
                $value = $option['value'];
                if (is_array($value)) {
                    $values = array_merge($values, $this->_getAttributeAllowedValues($value));
                } else {
                    $values[] = $value;
                }
            }
        }

        return $values;
    }

    /**
     * Set additional data before product save
     *
     * @param Mage_Catalog_Model_Product $product
     * @param array $productData
     */
    protected function _prepareDataForSave($product, $productData)
    {
        if (isset($productData['stock_data'])) {
            $this->_filterStockData($productData['stock_data']);
            $product->setStockData($productData['stock_data']);
        }

        if (isset($productData['website_ids']) && is_array($productData['website_ids'])) {
            $product->setWebsiteIds($productData['website_ids']);
        }
        /** @var $attribute Mage_Catalog_Model_Resource_Eav_Attribute */
        foreach ($product->getTypeInstance(true)->getEditableAttributes($product) as $attribute) {
            //Unset data if object attribute has no value in current store
            if (Mage_Catalog_Model_Abstract::DEFAULT_STORE_ID !== (int)$product->getStoreId()
                && !$product->getExistsStoreValueFlag($attribute->getAttributeCode())
                && !$attribute->isScopeGlobal()) {
                $product->setData($attribute->getAttributeCode(), false);
            }

            if ($this->_isAllowedAttribute($attribute)) {
                if (isset($productData[$attribute->getAttributeCode()])) {
                    $product->setData(
                        $attribute->getAttributeCode(),
                        $productData[$attribute->getAttributeCode()]
                    );
                }
            }
        }
    }

    /**
     * Filter stock data values
     *
     * @param array $stockData
     */
    protected function _filterStockData(&$stockData)
    {
        $fieldsWithPossibleDefautlValuesInConfig = array('manage_stock', 'min_sale_qty', 'max_sale_qty', 'backorders',
            'qty_increments', 'notify_stock_qty', 'min_qty');
        foreach($fieldsWithPossibleDefautlValuesInConfig as $field) {
            if ($this->_isConfigValueUsed($stockData, $field)) {
                unset($stockData[$field]);
            }
        }

        if (!isset($stockData['use_config_manage_stock'])) {
            $stockData['original_inventory_qty'] = 0;
        }
        if (isset($stockData['qty']) && (float)$stockData['qty'] > self::MAX_DECIMAL_VALUE) {
            $stockData['qty'] = self::MAX_DECIMAL_VALUE;
        }
        if (isset($stockData['min_qty']) && (int)$stockData['min_qty'] < 0) {
            $stockData['min_qty'] = 0;
        }
        if (!isset($stockData['is_decimal_divided']) || $stockData['is_qty_decimal'] == 0) {
            $stockData['is_decimal_divided'] = 0;
        }
    }

    /**
     * Check if attribute is allowed
     *
     * @param Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param array $attributes
     * @return boolean
     */
    protected function _isAllowedAttribute($attribute, $attributes = null)
    {
        if (is_array($attributes) && !(in_array($attribute->getAttributeCode(), $attributes)
            || in_array($attribute->getAttributeId(), $attributes))) {
            return false;
        }

        $ignoredAttributeTypes = array();
        $ignoredAttributeCodes = array('entity_id', 'attribute_set_id', 'entity_type_id');

        return !in_array($attribute->getFrontendInput(), $ignoredAttributeTypes)
            && !in_array($attribute->getAttributeCode(), $ignoredAttributeCodes);
    }

    /**
     * Check if value from config is used
     *
     * @param array $data
     * @param string $field
     * @return bool
     */
    protected function _isConfigValueUsed($data, $field)
    {
        return isset($data["use_config_$field"]) && $data["use_config_$field"];
    }
}
