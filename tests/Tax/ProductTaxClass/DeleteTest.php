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
 * @category    tests
 * @package     selenium
 * @subpackage  tests
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Product Tax Class deletion tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Tax_ProductTaxClass_DeleteTest extends Mage_Selenium_TestCase
{

    /**
     * <p>Save rule name for clean up</p>
     */
    protected $ruleToBeDeleted = null;

    /**
     * <p>Log in to Backend.</p>
     */
    public function setUpBeforeTests()
    {
        $this->loginAdminUser();
    }

    /**
     * <p>Create Tax Rate for tests<p>
     *
     * @return array $taxRateData
     * @test
     */
    public function setupTestDataCreateTaxRate()
    {
        //Data
        $taxRateData = $this->loadData('tax_rate_create_test', null, 'tax_identifier');
        //Steps
        $this->navigate('manage_tax_zones_and_rates');
        $this->taxHelper()->createTaxRate($taxRateData);
        $this->assertTrue($this->successMessage('success_saved_tax_rate'), $this->messages);
        return $taxRateData;
    }

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Sales-Tax-Product Tax Classes</p>
     */
    protected function assertPreConditions()
    {
        $this->navigate('manage_product_tax_class');
    }

    /**
     * <p>Delete a Product Tax Class</p>
     * <p>Steps:</p>
     * <p>1. Create a new Product Tax Class</p>
     * <p>2. Open the Product Tax Class</p>
     * <p>3. Delete the Product Tax Class</p>
     * <p>Expected result:</p>
     * <p>Received the message that the Product Tax Class has been deleted.</p>
     *
     * @test
     */
    public function notUsedInRule()
    {
        //Data
        $productTaxClassData = $this->loadData('new_product_tax_class', null, 'product_class_name');
        //Steps
        $this->taxHelper()->createTaxItem($productTaxClassData);
        $this->assertTrue($this->successMessage('success_saved_tax_class'), $this->messages);
        $this->taxHelper()->deleteTaxItem($productTaxClassData ,'product_tax_class');
        //Verifying
        $this->assertTrue($this->successMessage('success_deleted_tax_class'), $this->messages);
    }

    /**
     * <p>Delete a Product Tax Class that used in Tax Rule</p>
     * <p>Steps:</p>
     * <p>1. Create a new Product Tax Class</p>
     * <p>2. Create a new Tax Rule that use Product Tax Class from previous step</p>
     * <p>2. Open the Product Tax Class</p>
     * <p>3. Delete the Product Tax Class</p>
     * <p>Expected result:</p>
     * <p>Received the message that the Product Tax Class could not be deleted.</p>
     *
     * @depends setupTestDataCreateTaxRate
     * @param array $taxRateData
     * @test
     */
    public function usedInRule($taxRateData)
    {
        //Data
        $productTaxClassData = $this->loadData('new_product_tax_class', null, 'product_class_name');
        $taxRuleData = $this->loadData('new_tax_rule_required',
                                       array('tax_rate' => $taxRateData['tax_identifier'],
                                            'product_tax_class' => $productTaxClassData['product_class_name']),'name');
        $searchTaxRuleData = $this->loadData('search_tax_rule',
                                             array('filter_name' => $taxRuleData['name']));
        //Steps
        $this->taxHelper()->createTaxItem($productTaxClassData);
        $this->assertTrue($this->successMessage('success_saved_tax_class'), $this->messages);
        $this->navigate('manage_tax_rule');
        $this->taxHelper()->createTaxItem($taxRuleData);
        $this->assertTrue($this->successMessage('success_saved_tax_rule'), $this->messages);
        $this->ruleToBeDeleted = $searchTaxRuleData;      //For Clean Up
        $this->navigate('manage_product_tax_class');
        $this->taxHelper()->deleteTaxItem($productTaxClassData ,'product_tax_class');
        //Verifying
        $this->assertTrue($this->errorMessage('error_delete_tax_class'), $this->messages);
    }

    /**
     * <p>Delete a Product Tax Class that used in Product</p>
     * <p>Steps:</p>
     * <p>1. Create a new Product Tax Class</p>
     * <p>2. Create a new Product that use Product Tax Class from previous step</p>
     * <p>2. Open the Product Tax Class</p>
     * <p>3. Delete the Product Tax Class</p>
     * <p>Expected result:</p>
     * <p>Received the message that the Product Tax Class could not be deleted.</p>
     * <p>Error message: You cannot delete this tax class as it is used for 1 products.</p>
     *
     * @test
     */
    public function usedInProduct()
    {
        //Data
        $productTaxClassData = $this->loadData('new_product_tax_class', null, 'product_class_name');
        $productData = $this->loadData('simple_product_required',
                                       array('prices_tax_class' => $productTaxClassData['product_class_name']),
                                       array('general_name', 'general_sku'));
        //Steps
        $this->taxHelper()->createTaxItem($productTaxClassData);
        $this->assertTrue($this->successMessage('success_saved_tax_class'), $this->messages);
        $this->navigate('manage_products');
        $this->productHelper()->createProduct($productData);
        $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
        $this->navigate('manage_product_tax_class');
        $this->taxHelper()->deleteTaxItem($productTaxClassData ,'product_tax_class');
        //Verifying
        $this->assertTrue($this->errorMessage('error_delete_tax_class_product'), $this->messages);
    }

    /**
     * Clean up
     */
    protected function tearDown()
    {
        //Remove Tax rule after test
        if (!is_null($this->ruleToBeDeleted)) {
            $this->navigate('manage_tax_rule');
            $this->taxHelper()->deleteTaxItem($this->ruleToBeDeleted ,'tax_rules');
            $this->ruleToBeDeleted = null;
        }
    }

}
