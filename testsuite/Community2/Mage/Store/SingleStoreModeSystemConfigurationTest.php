<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Store
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */

class Community2_Mage_Store_SingleStoreModeSystemConfigurationTest extends Mage_Selenium_TestCase
{
    protected function assertPreConditions()
    {
        $this->loginAdminUser();
        $this->admin('manage_stores');
        $this->storeHelper()->deleteStoreViewsExceptSpecified();
    }

    /**
     * <p>Scope Selector is disabled if Single Store Mode enabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Manage Stores</p>
     * <p>3.Verify that one store-view is created.</p>
     * <p>4.Go to System - Configuration.</p>
     * <p>Expected result: </p>
     * <p>Scope Selector is not displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6180
     */
    function verificationScopeSelector()
    {
        $this->admin('system_configuration');
        $this->assertFalse($this->controlIsPresent('dropdown', 'current_configuration_scope'),
            "Scope Selector is present");
    }

    /**
     * <p>"Export Table Rates" functionality is displayed if Single Store Mode enabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Manage Stores</p>
     * <p>3.Verify that one store-view is created.</p>
     * <p>4.Go to System - Configuration - Sales - Shipping Methods.</p>
     * <p>5.Check for "Table Rates" fieldset  </p>
     * <p>Expected result: </p>
     * <p>"Export CSV" button is displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6181
     */
    function verificationTableRatesExport()
    {
        $this->admin('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('sales_shipping_methods');
        $button = 'table_rates_export_csv';
        $this->assertTrue($this->buttonIsPresent('table_rates_export_csv'), "Button $button is not present on the page");
    }

    /**
     * <p>"Account Sharing Options" fieldset is displayed if Single Store Mode enabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Manage Stores</p>
     * <p>3.Verify that one store-view is created.</p>
     * <p>4.Go to System - Configuration - Customer - Customer Configuration</p>
     * <p>5.Check for "Account Sharing Options" fieldset  </p>
     * <p>Expected result:</p>
     * <p>"Account Sharing Options" fieldset is displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6182
     */
    function verificationAccountSharingOptions()
    {
        $this->admin('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('customers_customer_configuration');
        $fieldset = 'account_sharing_options';
        $this->assertTrue($this->controlIsPresent('fieldset', $fieldset), "Fieldset $fieldset is not present on the page");
    }

    /**
     * <p>"Price" fieldset is displayed if Single Store Mode enabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Manage Stores</p>
     * <p>3.Verify that one store-view is created.</p>
     * <p>4.Go to System - Configuration - Catalog - Catalog</p>
     * <p>5.Check for "Price" fieldset</p>
     * <p>Expected result: </p>
     * <p>"Price" fieldset is displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6183
     */
    function verificationCatalogPrice()
    {
        $this->admin('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('catalog_catalog');
        $fieldset = 'price';
        $this->assertTrue($this->controlIsPresent('fieldset', $fieldset), "Fieldset $fieldset is not present on the page");
    }

    /**
     * <p>"Debug" fieldset is displayed if Single Store Mode enabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Manage Stores</p>
     * <p>3.Verify that one store-view is created.</p>
     * <p>4.Go to System - Configuration - Advanced - Developer</p>
     * <p>5.Check for "Debug" fieldset.</p>
     * <p>Expected result:</p>
     * <p>"Debug" fieldset is displayed.</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6184
     */
    function verificationDebugOptions()
    {
        $this->admin('system_configuration');
        $this->systemConfigurationHelper()->openConfigurationTab('advanced_developer');
        $fieldset = 'debug';
        $this->assertTrue($this->controlIsPresent('fieldset', $fieldset), "Fieldset $fieldset is not present on the page");
    }

    /**
     *<p>Hints for fields are disabled if Single Store Mode enabled.</p>
     * <p>Steps:</p>
     * <p>1.Login to backend.</p>
     * <p>2.Go to System - Manage Stores</p>
     * <p>3.Verify that one store-view is created.</p>
     * <p>4.Go to System - Configuration</p>
     * <p>5.Open required tab and fieldset and check hints</p>
     * <p>Expected result: </p>
     * <p>Hints are not displayed</p>
     *
     * @test
     * @TestlinkId TL-MAGE-6185
     */
    function verificationHints()
    {
        $this->admin('system_configuration');
        $locatorParts = array($this->_getControlXpath('pageelement', 'store_view_hint'),
                              $this->_getControlXpath('pageelement', 'global_view_hint'),
                              $this->_getControlXpath('pageelement', 'website_view_hint'));
        $needTypes = array(self::FIELD_TYPE_MULTISELECT, self::FIELD_TYPE_DROPDOWN, self::FIELD_TYPE_INPUT);

        $tabs = $this->getCurrentUimapPage()->getMainForm()->getAllTabs();
        /**
         * @var Mage_Selenium_Uimap_Tab $tabUimap
         */
        foreach ($tabs as $tabName => $tabUimap) {
            $this->openTab($tabName);
            $uimapFields = $tabUimap->getTabElements($this->getParamsHelper());
            foreach ($needTypes as $fieldType) {
                if (!isset($uimapFields[$fieldType])) {
                    continue;
                }
                foreach ($uimapFields[$fieldType] as $fieldName => $fieldLocator) {
                    foreach ($locatorParts as $part) {
                        if (!$this->elementIsPresent($fieldLocator . $part)) {
                            $this->addVerificationMessage(
                                "Element '" . $fieldName . "' is not on the page. Locator: " . $fieldLocator . $part);
                        }
                    }
                }
            }
        }
        $this->assertEmptyVerificationErrors();
    }
}