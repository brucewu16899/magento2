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
 * Compare Products tests
 *
 * @package     selenium
 * @subpackage  tests
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class CompareProducts_CompareProductsTest extends Mage_Selenium_TestCase
{

    /**
     *
     */
    public function setUpBeforeTests()
    {

    }

    /**
     * <p>Preconditions:</p>
     * <p>Remove all products from CompareProducts(Home page?)</p>
     */
    protected function assertPreConditions()
    {
        $this->frontend();
        $this->compareProductsHelper()->frontClearAll();
    }

    /**
     * <p>Create Category for tests<p>
     *
     * @return array Category 'name' and 'path'
     *
     * @test
     */
    public function setupTestDataCreateCategory()
    {
        $this->loginAdminUser();
        $this->navigate('manage_categories');
        $this->categoryHelper()->checkCategoriesPage();
        //Data
        $rootCat = $this->loadData('compare_default_root_category', null, null);
        $categoryData = $this->loadData('compare_sub_category_required', null, 'name');
        //Steps
        $this->categoryHelper()->createSubCategory($rootCat, $categoryData);
        //Verifying
        $this->assertTrue($this->successMessage('success_saved_category'), $this->messages);
        return array('name' => $categoryData['name'], 'path' => $rootCat . '/' . $categoryData['name']);
    }

    /**
     * <p>Create products for test<p>
     *
     * @depends setupTestDataCreateCategory
     * @param array $categoryData
     * @return array $productsData 
     *
     * @test
     */
    public function setupTestDataCreateProducts($categoryData)
    {
        $this->loginAdminUser();
        $this->navigate('manage_products');
        $this->addParameter('id', '0');
        for ($index = 0; $index < 2; $index++) {
            //Data
            $productData = $this->loadData('compare_products_simple_product', null,
                    array('general_name', 'general_sku'));
            $productData["categories"] = $categoryData['path'];
            //Steps
            $this->productHelper()->createProduct($productData);
            //Verifying
            $this->assertTrue($this->successMessage('success_saved_product'), $this->messages);
            $productsData[] = $productData;
        }
        return $productsData;
    }

    /**
     * <p>Adds a product to Compare Products from Product Details page.</p>
     * <p>Steps:</p>
     * <p>1. Open product</p>
     * <p>2. Add product to Compare Products</p>
     * <p>Expected result:</p>
     * <p>Success message is displayed.Product is displayed in Compare Products pop-up window</p>
     *
     * @depends setupTestDataCreateProducts
     * @param array $productsData
     *
     * @test
     */
    public function addProductToCompareListFromProductPage($productsData)
    {
        //Setup
        $this->compareProductsHelper()->frontAddProductToCompareFromProductPage(
                $productsData[0]['general_name']);
        $this->appendParamsDecorator($this->compareProductsHelper()->_paramsHelper); //@TODO Temporary workaround
        $this->assertTrue($this->successMessage('product_added_to_comparison'), $this->messages);
        $this->assertTrue($this->controlIsPresent('link', 'product_link'),
                'Product is not available in Compare widget');
        //Steps
        $this->compareProductsHelper()->frontOpenComparePopup();
        //Verify
        $this->assertTrue($this->controlIsPresent('link', 'product_title'),
                'There is no expected product in Compare Products popup');
    }

    /**
     * <p>Remove a product from CompareProducts block</p>
     * <p>Steps:</p>
     * <p>1. Open product</p>
     * <p>2. Add two products to CompareProducts</p>
     * <p>3. Remove one product from Compare Products</p>
     * <p>Expected result:</p>
     * <p>Product should disappear from the Compare Products block.</p>
     * <p>Success message is displayed</p>
     * <p>Product should not be dispalyed in the Compare Products pop-up</p>
     *
     * @depends setupTestDataCreateProducts
     * @param array $productsData
     *
     * @test
     */
    public function removeProductFromCompareList($productsData)
    {
        //Setup
        foreach ($productsData as $product) {
            $this->compareProductsHelper()->frontAddProductToCompareFromProductPage(
                    $product['general_name']);
            $this->appendParamsDecorator($this->compareProductsHelper()->_paramsHelper);  //@TODO Temporary workaround
            $this->assertTrue($this->successMessage('product_added_to_comparison'), $this->messages);
            $this->assertTrue($this->controlIsPresent('link', 'product_link'),
                    'Product is not available in Compare widget');
        }
        //Steps
        $this->compareProductsHelper()->frontRemoveProductFromCompareBlock($productsData[0]['general_name']);
        $this->addParameter('productName', $productsData[0]['general_name']);
        //Verify
        $this->assertTrue($this->successMessage('product_removed_from_comparison'), $this->messages);
        $this->assertFalse($this->controlIsPresent('link', 'product_link'),
                'There is unexpected product in Compare Products widget');
        $this->compareProductsHelper()->frontOpenComparePopup();
        $this->assertFalse($this->controlIsPresent('link', 'product_title'),
                'There is unexpected product in Compare Products popup');
    }

    /**
     * <p>Compare Products block is not dispalyed without products</p>
     * <p>Steps:</p>
     * <p>1. Open product</p>
     * <p>2. Add product to Compare Products</p>
     * <p>3. Remove product from Compare Products</p>
     * <p>Expected result:</p>
     * <p>Compare Products block should be empty</p>
     *
     * @depends setupTestDataCreateProducts
     * @param array $productsData
     *
     * @test
     */
    public function emptyCompareListIsNotAvailable($productsData)
    {
        //Setup
        $this->compareProductsHelper()->frontAddProductToCompareFromProductPage(
                $productsData[0]['general_name']);
        $this->appendParamsDecorator($this->compareProductsHelper()->_paramsHelper);        //@TODO Temporary workaround
        $this->assertTrue($this->successMessage('product_added_to_comparison'), $this->messages);
        $this->assertTrue($this->controlIsPresent('link', 'product_link'),
                'Product is not available in Compare widget');
        //Steps
        $this->compareProductsHelper()->frontClearAll();
        $this->assertTrue($this->successMessage('compare_list_cleared'), $this->messages);
        //Verify
        $this->assertTrue($this->controlIsPresent('pageelement', 'empty_comapre_block'),
                'There is unexpected product(s) in Compare Products widget');
    }

    /**
     * <p>Adds a products to Compare Products from Categoty page.</p>
     * <p>Steps:</p>
     * <p>1. Open product</p>
     * <p>2. Add first product to Compare Products</p>
     * <p>3. Add second product to Compare Products</p>
     * <p>Expected result:</p>
     * <p>Success message is displayed.Products displayed in Compare Products pop-up window</p>
     *
     * @depends setupTestDataCreateProducts
     * @depends setupTestDataCreateCategory
     * @param array $productsData
     * @param array $categoryData
     *
     * @test
     */
    public function addProductsToCompareListFromCatalogPage($productsData, $categoryData)
    {
        //Setup
        foreach ($productsData as $product) {
            $this->compareProductsHelper()->frontAddProductToCompareFromCatalogPage(
                    $product['general_name'], $categoryData['name']);
            $this->appendParamsDecorator($this->compareProductsHelper()->_paramsHelper);   //@TODO Temporary workaround
            $this->assertTrue($this->successMessage('product_added_to_comparison'), $this->messages);
            $this->assertTrue($this->controlIsPresent('link', 'product_link'),
                    'Product is not available in Compare widget');
        }
        //Steps
        $this->compareProductsHelper()->frontOpenComparePopup();
        $dataToVerify = $this->compareProductsHelper()->prepareProductForVerify($productsData);
        //Verify
        $testResult = $this->compareProductsHelper()->frontVerifyProductDataInComparePopup($dataToVerify);
        $this->assertTrue(empty($testResult['error']), $testResult);
    }

    public function tearDown()
    {
        $this->compareProductsHelper()->frontCloseComparePopup();
    }

}
