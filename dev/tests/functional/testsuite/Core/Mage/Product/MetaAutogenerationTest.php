<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Autogenerated Meta Description, Meta Keywords, Meta Title fields
 */
class Core_Mage_Product_MetaAutoGenerationTest extends Mage_Selenium_TestCase
{
    public static $placeholders = array('{{name}}', '{{sku}}', '{{description}}');

    /**
     * <p>Preconditions:</p>
     *  <p>1. Log in to admin</p>
     *  <p>2. Navigate System - Configuration</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('system_configuration');
    }

    /**
     * <p>Preconditions for tests:</p>
     *  <p>1. Setup Mask for SKU auto-generation</p>
     *  <p>2. Create attribute set</p>
     *
     * @return string
     *
     * @test
     */
    public function preconditionsForTests()
    {
        //Data
        $systemConfig =
            $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks', array('sku_mask' => '{{name}}'));
        $attributeSet = $this->loadDataSet('AttributeSet', 'attribute_set');
        //Setup config
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($systemConfig);
        //Create attribute set
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->createAttributeSet($attributeSet);
        //Verifying
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return $attributeSet['set_name'];
    }

    /**
     * <p>1. Set default values for Meta fields AutoGeneration mask.</p>
     * <p>2. Set Meta attributes as non-required and without default values</p>
     */
    protected function tearDownAfterTestClass()
    {
        //System settings
        $systemConfig = $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks',
            array('meta_title_mask' => '{{name}}', 'meta_description_mask' => '{{name}} {{description}}',
                  'meta_keyword_mask' => '{{name}}', 'sku_mask' => '{{name}}'));
        $this->navigate('system_configuration');
        $this->systemConfigurationHelper()->configure($systemConfig);
        //System attributes
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()
            ->editAttribute('meta_title', array('default_text_field_value' => '', 'values_required' => 'No'));
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->productAttributeHelper()
            ->editAttribute('meta_description', array('default_text_area_value' => '', 'values_required' => 'No'));
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->productAttributeHelper()
            ->editAttribute('meta_keyword', array('default_text_area_value' => '', 'values_required' => 'No'));
        $this->assertMessagePresent('success', 'success_saved_attribute');
    }

    /**
     * <p>Meta Tab auto-generation verification</p>
     * <p>Preconditions:</p>
     *  <p>1a. Mask for Meta Title auto-generation = {{name}}</p>
     *  <p>1b. Mask for Meta Keyword auto-generation = {{name}},{{sku}}</p>
     *  <p>1c. Mask for Meta Description auto-generation = {{name}} {{description}}</p>
     *
     * @param $metaCode
     * @param $metaField
     * @param $metaMask
     *
     * @test
     * @dataProvider defaultMetaMaskDataProvider
     * @TestLinkId TL-MAGE-6164
     */
    public function verifyDefaultMask($metaCode, $metaField, $metaMask)
    {
        //Preconditions
        $systemConfig = $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks',
            array($metaCode . '_mask' => $metaMask));
        $this->systemConfigurationHelper()->configure($systemConfig);
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple', false);
        $testData = $this->_formFieldValueFromMask($metaMask, self::$placeholders);
        $this->productHelper()->saveProduct();
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->openProductTab('meta_information');
        $this->assertEquals($testData, $this->getControlAttribute('field', $metaField, 'value'));
    }

    /**
     * <p>DataProvider for verify default mask for Meta Fields Auto-Generation</p>
     *
     * @return array
     */
    public function defaultMetaMaskDataProvider()
    {
        return array(
            array('meta_title', 'meta_information_meta_title', '{{name}}'),
            array('meta_description', 'meta_information_meta_description', '{{name}} {{description}}'),
            array('meta_keyword', 'meta_information_meta_keywords', '{{name}}')
        );
    }

    /**
     * <p>Verifying, that autogeneration of meta fields doesn't work for product duplication</p>
     *
     * @test
     * @TestLinkId TL-MAGE-6165
     */
    public function duplicateSimple()
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Preconditions
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertMessagePresent('success', 'success_saved_product');
        //Steps
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_name']));
        $this->productHelper()->openProductTab('meta_information');
        $metaKeywords = $this->getControlAttribute('field', 'meta_information_meta_keywords', 'value');
        $this->productHelper()->saveProduct('duplicate');
        //Verifying
        $this->assertMessagePresent('success', 'success_duplicated_product');
        $this->waitForControlEditable('field', 'general_name');
        $this->fillField('general_name', 'Name#2');
        $this->productHelper()->saveProduct();
        $this->assertMessagePresent('success', 'success_saved_product');
        $productData['general_sku'] = $this->productHelper()->getGeneratedSku($productData['general_sku']);
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->verifyProductInfo(array('general_name'=> 'Name#2',
            'general_sku' => $productData['general_sku']));
        $this->productHelper()->openProductTab('meta_information');
        $this->assertEquals($metaKeywords,
            $this->getControlAttribute('field', 'meta_information_meta_keywords', 'value'));
    }

    /**
     * <p>Meta fields Auto-generation template verification</p>
     *
     * @param string $metaCode
     * @param string $metaField
     * @param string $metaMask
     *
     * @test
     * @dataProvider templateMetaMaskDataProvider
     * @TestLinkId TL-MAGE-6179
     */
    public function verifyMaskTemplates($metaCode, $metaField, $metaMask)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Preconditions
        $systemConfig = $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks',
            array($metaCode . '_mask' => $metaMask));
        $this->systemConfigurationHelper()->configure($systemConfig);
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple', false);
        $testData = $this->_formFieldValueFromMask($metaMask, self::$placeholders);
        $this->productHelper()->saveProduct();
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_sku']));
        $this->productHelper()->openProductTab('meta_information');
        $this->assertEquals($testData, $this->getControlAttribute('field', $metaField, 'value'));
    }

    /**
     * <p>DataProvider for verify different masks for Meta Fields Auto-Generation</p>
     *
     * @return array
     */
    public function templateMetaMaskDataProvider()
    {
        return array(
            array('meta_title', 'meta_information_meta_title', $this->generate('string', 56, ':alnum:') . '{{name}}'),
            array('meta_description', 'meta_information_meta_description',
                '{{name}}' . $this->generate('string', 41, ':alnum:') . '{{description}}'),
            array('meta_keyword', 'meta_information_meta_keywords',
                '{{name}}, {{sku}}' . $this->generate('string', 47, ':alnum:')),
            array('meta_title', 'meta_information_meta_title', $this->generate('string', 32, ':punct:') . '{{name}}'),
            array('meta_description', 'meta_information_meta_description',
                '{{name}}' . $this->generate('string', 32, ':punct:') . '{{description}}'),
            array('meta_keyword', 'meta_information_meta_keywords',
                '{{name}}, {{sku}}' . $this->generate('string', 32, ':punct:')),
            array('meta_title', 'meta_information_meta_title', 'name' . ' ' . $this->generate('string', 10, ':alpha:')),
            array('meta_description', 'meta_information_meta_description',
                '{{name}}' . 'description' . '{{short_description}}'),
            array('meta_keyword', 'meta_information_meta_keywords',
                'sku' . ' ' . ' name' . ' ' . $this->generate('string', 10, ':alpha:')),
            array('meta_title', 'meta_information_meta_title', '{{weight}} {{name}}'),
            array('meta_description', 'meta_information_meta_description', '{{nonexisted_attribute}}, {{name}}'),
            array('meta_keyword', 'meta_information_meta_keywords',
                '{{name}}, {{name}}, {{name}}, {{name}}, {{name}}, {{name}}, {{name}}, {{name}}, {{name}}')
        );
    }

    /**
     * <p>Verify, that autogeneration of Meta fields and SKU enabled if</p>
     * <p>Attribute set has been changed before enter product data</p>
     *
     * @param string $metaCode
     * @param string $metaField
     * @param string $metaMask
     * @param string $attributeSet
     *
     * @test
     * @dataProvider templateMetaMaskDataProvider
     * @depends preconditionsForTests
     * @TestLinkId TL-MAGE-6214
     */
    public function afterChangeAttributeSet($metaCode, $metaField, $metaMask, $attributeSet)
    {
        $this->markTestIncomplete('MAGETWO-7054');
        //Data
        $systemConfig = $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks',
            array($metaCode . '_mask' => $metaMask, 'sku_mask' => '{{name}}'));
        $productData =
            $this->loadDataSet('Product', 'simple_product_required', array('product_attribute_set' => $attributeSet));
        unset ($productData['general_sku']);
        //Preconditions
        $this->systemConfigurationHelper()->configure($systemConfig);
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple', false);
        $this->productHelper()->openProductTab('general');
        $testData = $this->_formFieldValueFromMask($metaMask, self::$placeholders);
        $this->productHelper()->saveProduct();
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_name']));
        $this->assertEquals($productData['general_name'], $this->getControlAttribute('field', 'general_sku', 'value'));
        $this->productHelper()->openProductTab('meta_information');
        $this->assertEquals($testData, $this->getControlAttribute('field', $metaField, 'value'));
    }

    /**
     * <p>Meta Fields auto-generation is disabled if default value for meta attribute has been defined</p>
     *
     * @param string $metaCode
     * @param string $metaField
     * @param string $fieldType
     * @param string $mask
     *
     * @test
     * @dataProvider metaFieldsDataProvider
     * @TestLinkId TL-MAGE-6193
     */
    public function textAttributeDefaultValue($metaCode, $metaField, $fieldType, $mask)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $editedElement = $this->generate('string', 15, ':alnum:');
        //Preconditions
        $systemConfig = $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks',
            array($metaCode . '_mask' => $mask));
        $this->systemConfigurationHelper()->configure($systemConfig);
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()
            ->editAttribute($metaCode, array('default_' . $fieldType . '_value' => $editedElement));
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_name']));
        $this->productHelper()->openProductTab('meta_information');
        $this->assertEquals($editedElement, $this->getControlAttribute('field', $metaField, 'value'));
    }

    public function metaFieldsDataProvider()
    {
        return array(
            array('meta_title', 'meta_information_meta_title', 'text_field', '{{name}}'),
            array('meta_description', 'meta_information_meta_description', 'text_area', '{{name}} {{description}}'),
            array('meta_keyword', 'meta_information_meta_keywords', 'text_area', '{{name}}')
        );
    }

    /**
     * <p>Create product with user-defined values for Meta Tags</p>
     *
     * @param string $metaCode
     * @param string $metaField
     * @param string $fieldType
     *
     * @test
     * @dataProvider defaultMetaMaskDataProvider
     * @TestLinkId TL-MAGE-6194
     */
    public function saveWithUserDefinedValues($metaCode, $metaField, $fieldType)
    {
        //Preconditions
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()
            ->editAttribute($metaCode, array('values_required' => 'No', 'default_' . $fieldType . '_value' => ''));
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        $metaMask = $this->generate('string', 255, ':alnum:');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple', false);
        $this->productHelper()->openProductTab('meta_information');
        $this->fillField($metaField, $metaMask);
        $this->productHelper()->saveProduct();
        //Verifying
        $productData[$metaField] = $metaMask;
        $this->assertMessagePresent('success', 'success_saved_product');
        $this->productHelper()->openProduct(array('product_sku' => $productData['general_name']));
        $this->productHelper()->verifyProductInfo($productData);
    }

    /**
     * <p>Verify that product with Meta fields autogeneration has been created without verification errors </p>
     * <p>when meta attributes set as required</p>
     *
     * @test
     * @TestLinkId TL-MAGE-6192
     */
    public function setMetaTabRequired()
    {
        //Data
        $metaAttributes = array('meta_title', 'meta_keyword', 'meta_description');
        $editedElement = array('values_required' => 'Yes');
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Steps
        $this->navigate('manage_attributes');
        foreach ($metaAttributes as $value) {
            $this->productAttributeHelper()->editAttribute($value, $editedElement);
            $this->assertMessagePresent('success', 'success_saved_attribute');
        }
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        //Verifying
        $this->assertMessagePresent('success', 'success_saved_product');
    }

    /**
     * <p>Meta fields Auto-generation is disabled if Autogeneration mask field is empty </p>
     *
     * @param string $metaCode
     * @param string $metaField
     * @param string $fieldType
     *
     * @test
     * @dataProvider metaFieldsDataProvider
     * @TestLinkId TL-MAGE-6191
     */
    public function emptyMetaMask($metaCode, $metaField, $fieldType)
    {
        //Data
        $productData = $this->loadDataSet('Product', 'simple_product_required');
        //Preconditions
        $systemConfig = $this->loadDataSet('FieldsAutogeneration', 'fields_autogeneration_masks',
            array($metaCode . '_mask'   => ''));
        $this->systemConfigurationHelper()->configure($systemConfig);
        $this->navigate('manage_attributes');
        $this->productAttributeHelper()
            ->editAttribute($metaCode, array('values_required' => 'Yes', 'default_' . $fieldType . '_value' => ''));
        $this->assertMessagePresent('success', 'success_saved_attribute');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData, 'simple', false);
        //Verifying
        $this->assertTrue($this->controlIsVisible('button', 'save_disabled'));
//        $this->productHelper()->openProductTab('meta_information');
//        $this->addFieldIdToMessage('field', $metaField);
//        $this->assertMessagePresent('validation', 'empty_required_field');
    }

    /**
     * Form mask's value replacing variable in mask with variable field's value on General tab
     *
     * @param string $mask
     * @param array $placeholders
     *
     * @return string
     */
    protected function _formFieldValueFromMask($mask, array $placeholders)
    {
        $this->productHelper()->openProductTab('general');
        foreach ($placeholders as $value) {
            $productField = 'general_' . str_replace(array('{{', '}}'), '', $value);
            $maskData = $this->getControlAttribute('field', $productField, 'value');
            $mask = str_replace($value, $maskData, $mask);
        }
        return $mask;
    }
}
