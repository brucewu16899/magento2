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
 * Creating rules with correct and incorrect data without applying them
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class PriceRules_ShoppingCart_CreateTest extends Mage_Selenium_TestCase
{
    /**
     * <p>Login to backend</p>
     */
    public function setUpBeforeTests()
    {}

    /**
     * <p>Preconditions:</p>
     * <p>Navigate to Catalog - Manage Products</p>
     */
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->addParameter('id', '0');
    }

    /**
     * <p>Create Shopping cart price rule with empty required fields.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions - Shopping Cart Price Rules;</p>
     * <p>2. Fill form for SCPR, but leave one required field empty (empty fields are listed in data provider);</p>
     * <p>3. Try to save newly created SCPR with one empty required field;</p>
     * <p>Expected results:</p>
     * <p>Rule is not created; Message "This is required field" is shown under each empty required field;</p>
     *
     * @dataProvider dataEmptyFields
     * @test
     */
    public function createWithEmptyRequiredFields($fieldName, $fieldType)
    {
        $this->navigate('manage_shopping_cart_price_rules');
        $dataToOverride = array();
        if ($fieldType == 'multiselect') {
            $dataToOverride[$fieldName] = '%noValue%';
        } else {
            $dataToOverride[$fieldName] = '';
        }
        $ruleData = $this->loadData('scpr_required_fields', $dataToOverride, array('rule_name', 'coupon_code'));
        $this->PriceRulesHelper()->createRule($ruleData);
        $this->addFieldIdToMessage($fieldType, $fieldName);
        if ($fieldName == 'discount_amount') {
            $this->assertTrue($this->validationMessage('enter_valid_number'), $this->messages);
        } else {
            $this->assertTrue($this->validationMessage('empty_required_field'), $this->messages);
        }
        $this->assertTrue($this->verifyMessagesCount(), $this->messages);
    }

    public function dataEmptyFields()
    {
        return array(
            array('rule_name', 'field'),
            array('customer_groups', 'multiselect'),
            array('coupon_code', 'field'),
            array('discount_amount', 'field')
        );
    }

    /**
     * <p>Create Shopping cart price rule with special symbols in fields.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions - Shopping Cart Price Rules;</p>
     * <p>2. Fill form for SCPR, but one field should be filled with special symbols (fields are listed in data provider);</p>
     * <p>3. Try to save newly created SCPR with one field filled with special symbols;</p>
     * <p>Expected results:</p>
     * <p>Rule is created where applicable;</p>
     *
     * @test
     */
    public function createWithRequiredFieldsWithSpecialSymbols()
    {
        $this->navigate('manage_shopping_cart_price_rules');
        $ruleData = $this->loadData('scpr_required_fields',
                array(
                    'rule_name'              => $this->generate('string', 32, ':punct:'),
                    'coupon_code'            => $this->generate('string', 32, ':punct:')
                ));
        $ruleSearch = $this->loadData('search_shopping_cart_rule',
                                      array('filter_rule_name' => $ruleData['info']['rule_name'],
                                            'filter_coupon_code' => $ruleData['info']['coupon_code']));
        $this->PriceRulesHelper()->createRule($ruleData);
        $this->assertTrue($this->successMessage('success_saved_rule'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
        $this->PriceRulesHelper()->openRule($ruleSearch);
        $this->PriceRulesHelper()->verifyRuleData($ruleData);
    }

    /**
     * <p>Create Shopping cart price rule with required fields only filled.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions - Shopping Cart Price Rules;</p>
     * <p>2. Fill form (only required fields) for SCPR;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>Expected results:</p>
     * <p>Rule is created;</p>
     *
     * @test
     */
    public function createWithRequiredFields()
    {
        $this->navigate('manage_shopping_cart_price_rules');
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
        $ruleData = $this->loadData('scpr_required_fields', NULL, array('rule_name', 'coupon_code'));
        $this->PriceRulesHelper()->createRule($ruleData);
        $this->assertTrue($this->successMessage('success_saved_rule'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);

    }

    /**
     * <p>Create Shopping cart price rule with all fields filled (except conditions).</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions - Shopping Cart Price Rules;</p>
     * <p>2. Fill form (all fields) for SCPR;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>Expected results:</p>
     * <p>Rule is created;</p>
     *
     * @test
     */
    public function createWithAllFields()
    {
        $this->navigate('manage_shopping_cart_price_rules');
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
        $ruleData = $this->loadData('scpr_all_fields', NULL, array('rule_name', 'coupon_code'));
        $this->PriceRulesHelper()->createRule($ruleData);
        $this->assertTrue($this->successMessage('success_saved_rule'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
    }

    /**
     * <p>Create Shopping cart price rule with all fields filled (except conditions).</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions - Shopping Cart Price Rules;</p>
     * <p>2. Fill form (all fields) for SCPR;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>Expected results:</p>
     * <p>Rule is created;</p>
     *
     * @test
     */
    public function createWithoutCoupon()
    {
        $this->navigate('manage_shopping_cart_price_rules');
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
        $ruleData = $this->loadData('scpr_all_fields',
                                    array('coupon'          => 'No Coupon',
                                          'coupon_code'     => '%noValue%',
                                          'uses_per_coupon' => '%noValue%'),
                                    array('rule_name'));
        $this->PriceRulesHelper()->createRule($ruleData);
        $this->assertTrue($this->successMessage('success_saved_rule'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
    }

    /**
     * <p>Create Shopping cart price rule with existing coupon.</p>
     * <p>Steps:</p>
     * <p>1. Navigate to Promotions - Shopping Cart Price Rules;</p>
     * <p>2. Fill form (all fields) for SCPR;</p>
     * <p>3. Save newly created SCPR;</p>
     * <p>Expected results:</p>
     * <p>Rule is created;</p>
     *
     * <p>4. Create rule with the same coupon;</p>
     * <p>Expected Results:</p>
     * <p>Rule is not created; Messsage "Coupon with the same code already exists." appears.</p>
     *
     * @test
     */
    public function createWithExistingCoupon()
    {
        $this->navigate('manage_shopping_cart_price_rules');
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
        $ruleData = $this->loadData('scpr_all_fields', NULL, array('rule_name', 'coupon_code'));
        $this->PriceRulesHelper()->createRule($ruleData);
        $this->assertTrue($this->successMessage('success_saved_rule'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
        $this->PriceRulesHelper()->createRule($ruleData);
        $this->assertTrue($this->errorMessage('error_coupon_code_exists'), $this->messages);
        $this->assertTrue($this->checkCurrentPage('manage_shopping_cart_price_rules'), $this->messages);
    }
}
