<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Reports\Test\Constraint;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Reports\Test\Page\Adminhtml\ProductLowStock;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertLowStockProductInGrid
 * Assert that product with Low Stock is present in Low Stock grid
 */
class AssertLowStockProductInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that product with Low Stock is present in Low Stock grid
     *
     * @param CatalogProductSimple $product
     * @param ProductLowStock $productLowStock
     * @return void
     */
    public function processAssert(CatalogProductSimple $product, ProductLowStock $productLowStock)
    {
        $productLowStock->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $productLowStock->getLowStockGrid()->isRowVisible(['name' => $product->getName()]),
            'Product with Low Stock is absent in Low Stock grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Product with Low Stock is present in Low Stock grid.';
    }
}
