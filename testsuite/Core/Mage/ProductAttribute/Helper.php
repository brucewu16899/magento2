<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ProductAttribute
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Helper class
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Core_Mage_ProductAttribute_Helper extends Mage_Selenium_AbstractHelper
{
    /**
     * Action_helper method for Create Attribute
     * Preconditions: 'Manage Attributes' page is opened.
     *
     * @param array $attrData Array which contains DataSet for filling of the current form
     */
    public function createAttribute($attrData)
    {
        $this->clickButton('add_new_attribute');
        $this->fillTab($attrData, 'properties', false);
        if (!$this->fillTab($attrData, 'manage_labels_options', false)) {
            $this->openTab('manage_labels_options');
        }
        $this->storeViewTitles($attrData);
        $this->attributeOptions($attrData);
        $this->saveForm('save_attribute');
    }

    /**
     * Open Product Attribute.
     * Preconditions: 'Manage Attributes' page is opened.
     *
     * @param array $searchData
     */
    public function openAttribute($searchData)
    {
        $searchData = $this->_prepareDataForSearch($searchData);
        $xpathTR = $this->search($searchData, 'attributes_grid');
        $this->assertNotNull($xpathTR, 'Attribute is not found');
        $this->addParameter('tableLineXpath', $xpathTR);
        $this->addParameter('cellIndex', $this->getColumnIdByName('Attribute Code'));
        $text = $this->getControlAttribute('pageelement', 'table_line_cell_index', 'text');
        $this->addParameter('elementTitle', $text);
        $this->addParameter('id', $this->defineIdFromTitle($xpathTR));
        $this->clickControl('pageelement', 'table_line_cell_index');
    }

    /**
     * Verify all data in saved Attribute.
     * Preconditions: Attribute page is opened.
     *
     * @param array $attrData
     */
    public function verifyAttribute($attrData)
    {
        $this->assertTrue($this->verifyForm($attrData, 'properties'), $this->getParsedMessages());
        $this->openTab('manage_labels_options');
        $this->storeViewTitles($attrData, 'manage_titles', 'verify');
        $this->attributeOptions($attrData, 'verify');
    }

    /**
     * Create Attribute from product page.
     * Preconditions: Product page is opened.
     *
     * @param array $attrData
     * @param string $saveInAttributeSet
     */
    public function createAttributeOnProductTab($attrData, $saveInAttributeSet = '')
    {
        // Defining and adding %fieldSetId% for Uimap pages.
        $tabUimap = $this->_getActiveTabUimap();
        list($activeFieldsetName) = $tabUimap->getFieldsetNames();
        $id = explode('_', $this->getControlAttribute('fieldset', $activeFieldsetName, 'id'));
        foreach ($id as $value) {
            if (is_numeric($value)) {
                $fieldSetId = $value;
                $this->addParameter('tabId', $fieldSetId);
                break;
            }
        }
        $productId = $this->defineIdFromUrl();
        $pageName = 'new_product_attribute_from_product_page';
        if (!is_null($productId)) {
            $this->addParameter('productId', $productId);
            $pageName = 'new_product_attribute_from_saved_product_page';
        }
        //Steps. Click 'Create New Attribute' button, select opened window.
        $this->clickButton('create_new_attribute', false);
        $this->selectLastWindow();
        $this->validatePage($pageName);
        $this->fillForm($attrData, 'properties');
        $this->fillForm($attrData, 'manage_labels_options');
        $this->storeViewTitles($attrData);
        $this->attributeOptions($attrData);
        //$this->addParameter('attributeId', 0);
        if ($saveInAttributeSet) {
            $messagesXpath = $this->getBasicXpathMessagesExcludeCurrent(array('success', 'error', 'validation'));
            $this->clickButton('save_in_new_attribute_set', false);
            $this->alertText($saveInAttributeSet);
            $this->acceptAlert();
            $this->waitForElementVisible($messagesXpath);
            $this->addParameter('setId', $this->defineParameterFromUrl('new_attribute_set_id'));
        } else {
            $this->saveForm('save_attribute', false);
        }
        $this->window('');
        if (isset($attrData['attribute_code'])) {
            $this->addParameter('elementId', $attrData['attribute_code']);
            $this->waitForElement("//*[contains(@id,'" . $attrData['attribute_code'] . "')]");
        }
        $this->validatePage();
    }

    /**
     * Fill or Verify Titles for different Store View
     *
     * @param array $attrData
     * @param string $fieldsetName
     * @param string $action
     */
    public function storeViewTitles($attrData, $fieldsetName = 'manage_titles', $action = 'fill')
    {
        $name = 'store_view_titles';
        if (isset($attrData['admin_title'])) {
            $attrData[$name]['Admin'] = $attrData['admin_title'];
        }
        if (array_key_exists($name, $attrData) && is_array($attrData[$name])) {
            $this->addParameter('tableHeadXpath', $this->_getControlXpath('fieldset', $fieldsetName));
            $qtyStore = $this->getControlCount('pageelement', 'table_column');
            foreach ($attrData[$name] as $storeViewName => $storeViewValue) {
                $number = -1;
                for ($i = 1; $i <= $qtyStore; $i++) {
                    $this->addParameter('index', $i);
                    if ($this->getControlAttribute('pageelement', 'table_column_index', 'text') == $storeViewName) {
                        $number = $i;
                        break;
                    }
                }
                if ($number != -1) {
                    $this->addParameter('storeViewNumber', $number);
                    $fieldName = preg_replace('/^manage_/', '', $fieldsetName) . '_by_store_name';
                    switch ($action) {
                        case 'fill':
                            $this->fillField($fieldName, $storeViewValue);
                            break;
                        case 'verify':
                            $actualText = $this->getControlAttribute('field', $fieldName, 'value');
                            $var = array_flip(get_html_translation_table());
                            $actualText = strtr($actualText, $var);
                            $this->assertEquals($storeViewValue, $actualText, 'Stored data not equals to specified');
                            break;
                    }
                } else {
                    $this->fail('Cannot find specified Store View with name \'' . $storeViewName . '\'');
                }
            }
        }
    }

    /**
     * Fill or Verify Options for Dropdown and Multiple Select Attributes
     *
     * @param array $attrData
     * @param string $action
     */
    public function attributeOptions($attrData, $action = 'fill')
    {
        $optionCount = $this->getControlCount('pageelement', 'manage_options_option');
        $number = 1;
        foreach ($attrData as $fKey => $dValue) {
            if (preg_match('/^option_/', $fKey) and is_array($attrData[$fKey])) {
                if ($this->controlIsPresent('fieldset', 'manage_options')) {
                    switch ($action) {
                        case 'fill':
                            $this->addParameter('fieldOptionNumber', $optionCount);
                            $this->clickButton('add_option', false);
                            $this->storeViewTitles($attrData[$fKey], 'manage_options');
                            $this->fillFieldset($attrData[$fKey], 'manage_options');
                            $optionCount = $this->getControlCount('pageelement', 'manage_options_option');
                            break;
                        case 'verify':
                            if ($optionCount-- > 0) {
                                $this->addParameter('index', $number++);
                                $optionNumber = $this->getControlAttribute('pageelement', 'is_default_option_index',
                                    'selectedValue');
                                $this->addParameter('fieldOptionNumber', $optionNumber);
                                $this->assertTrue($this->verifyForm($attrData[$fKey], 'manage_labels_options'),
                                    $this->getParsedMessages());
                                $this->storeViewTitles($attrData[$fKey], 'manage_options', 'verify');
                            }
                            break;
                    }
                }
            }
        }
    }

    /**
     * Define Attribute Id
     *
     * @param array $searchData
     *
     * @return int
     */
    public function defineAttributeId(array $searchData)
    {
        $this->navigate('manage_attributes');
        $attrXpath = $this->search($searchData, 'attributes_grid');
        $this->assertNotEquals(null, $attrXpath);

        return $this->defineIdFromTitle($attrXpath);
    }
}