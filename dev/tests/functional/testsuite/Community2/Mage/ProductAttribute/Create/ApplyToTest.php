<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ProductAttribute
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Check the possibility to set user-defined attribute for selected product type
 */
class Community2_Mage_ProductAttribute_Create_ApplyToTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Preconditions:</p>
     * <p>Navigate to System - Manage Attributes.</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->navigate('manage_attributes');
    }

    /**
     * <p>Create user-defined attribute which applied only to Simple Product type</p>
     *
     * @return array
     *
     * @test
     */
    public function createAttribute()
    {
        //Data
        $attributeData = $this->loadDataSet('ProductAttribute', 'product_attribute_dropdown',
            array('apply_to' => 'Selected Product Types', 'apply_product_types' => 'Simple Product'));
        $associatedAttribute = $this->loadDataSet('AttributeSet', 'associated_attributes',
            array('General' => $attributeData['attribute_code']));
        //Steps
        $this->productAttributeHelper()->createAttribute($attributeData);
        $this->assertMessagePresent('success', 'success_saved_attribute');
        $this->navigate('manage_attribute_sets');
        $this->attributeSetHelper()->openAttributeSet();
        $this->attributeSetHelper()->addAttributeToSet($associatedAttribute);
        $this->saveForm('save_attribute_set');
        $this->assertMessagePresent('success', 'success_attribute_set_saved');

        return array ('assigned_attribute' => $attributeData['attribute_code']);
        }

    /**
     * <p>Verify that Apply To dropdown is enabled for new user-defined product attribute</p>
     *
     * @test
     * @TestLinkId TL-MAGE-6424
     * @author Maryna_Ilnytska
     */
    public function checkApplyToEnabledForUserDefined()
    {
        //Steps
        $this->clickButton('add_new_attribute');
        $dropdownXpath = $this->_getControlXpath('dropdown', 'apply_to');
        //Verifying
        $this->assertTrue($this->isElementPresent($dropdownXpath), 'Apply To dropdown is absent');
        $this->assertTrue($this->isEditable($dropdownXpath), 'Apply To dropdown is disabled');
        $this->assertFalse($this->isElementPresent('apply_product_types'));
        $this->assertEquals('All Product Types', $this->getSelectedLabel($dropdownXpath));
        //Steps
        $this->fillDropdown('apply_to', 'Selected Product Types');
        $multiselectXpath = $this->_getControlXpath('multiselect', 'apply_product_types');
        $this->assertTrue($this->isElementPresent($multiselectXpath), 'Apply To multiselect is absent');
        $this->assertTrue($this->isEditable($multiselectXpath), 'Apply To multiselect is disabled');
    }

    /**
     * <p>Verify that selection in Apply To control is used for selected product type's template</p>
     *
     * @param array $attribute
     *
     * @test
     * @depends createAttribute
     * @TestLinkId TL-MAGE-6425
     * @author Maryna_Ilnytska
     */
    public function verifyDisplayingOnProductPage($attribute)
    {
        //Data
        $simple = $this->loadDataSet('Product', 'simple_product_required');
        $virtual = $this->loadDataSet('Product', 'virtual_product_required');
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($simple, 'simple', false);
        //Verifying presence for simple product type
        $this->openTab('general');
        $this->addParameter('attributeCodeDropdown', $attribute['assigned_attribute']);
        $attribute = $this->_getControlXpath('dropdown', 'general_user_attr_dropdown');
        $this->assertTrue($this->isElementPresent($attribute));
        //Steps
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($virtual, 'virtual', false);
        //Verifying absence for virtual product type
        $this->openTab('general');
        $this->assertFalse($this->isElementPresent($attribute));
    }
}
