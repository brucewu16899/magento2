<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Reports\Test\TestCase;

use Magento\Reports\Test\Page\Adminhtml\ProductReportView;
use Mtf\Client\Browser;
use Mtf\Fixture\FixtureFactory;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for ViewedProductsReportEntity
 *
 * Test Flow:
 * Preconditions:
 * 1. Create products
 * 2. Open product page on frontend
 * 3. Refresh statistic
 *
 * Steps:
 * 1. Open Backend
 * 2. Go to Reports> Products> Views
 * 3. Select time range, report period
 * 4. Click "Show report"
 * 5. Perform all assertions
 *
 * @group Reports_(MX)
 * @ZephyrId MAGETWO-27954
 */
class ViewedProductsReportEntityTest extends Injectable
{
    /**
     * Product Report View page
     *
     * @var ProductReportView
     */
    protected $productReportView;

    /**
     * Fixture factory
     *
     * @var FixtureFactory
     */
    protected $fixtureFactory;

    /**
     * Browser interface
     *
     * @var Browser
     */
    protected $browser;

    /**
     * Inject pages
     *
     * @param ProductReportView $productReportView
     * @param FixtureFactory $fixtureFactory
     * @param Browser $browser
     * @return void
     */
    public function __inject(
        ProductReportView $productReportView,
        FixtureFactory $fixtureFactory,
        Browser $browser
    ) {
        $this->productReportView = $productReportView;
        $this->fixtureFactory = $fixtureFactory;
        $this->browser = $browser;
    }

    /**
     * Viewed product report list
     *
     * @param string $products
     * @param array $viewsReport
     * @param string $total
     * @return array
     */
    public function test($products, array $viewsReport, $total)
    {
        $this->markTestIncomplete('MAGETWO-15707');
        // Preconditions
        $productsList = $this->prepareProducts($products);
        $this->openProducts($productsList, $total);
        $this->productReportView->open();
        $this->productReportView->getMessagesBlock()->clickLinkInMessages('notice', 'here');

        // Steps
        $this->productReportView->getFilterBlock()->viewsReport($viewsReport);
        $this->productReportView->getActionsBlock()->showReport();
        return ['productsList' => $productsList];
    }

    /**
     * Create products
     *
     * @param string $productList
     * @return array
     */
    protected function prepareProducts($productList)
    {
        $productsData = explode(', ', $productList);
        $products = [];
        foreach ($productsData as $productConfig) {
            $product = explode('::', $productConfig);
            $productFixture = $this->fixtureFactory->createByCode($product[0], ['dataSet' => $product[1]]);
            $productFixture->persist();
            $products[] = $productFixture;
        }
        return $products;
    }

    /**
     * Open products
     *
     * @param array $products
     * @param string $total
     * @return void
     */
    protected function openProducts(array $products, $total)
    {
        $total = explode(', ', $total);
        foreach ($products as $key => $product) {
            for ($i = 0; $i < $total[$key]; $i++) {
                $this->browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
            }
        }
    }
}
