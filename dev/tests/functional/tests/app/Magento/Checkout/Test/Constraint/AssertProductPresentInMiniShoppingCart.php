<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Checkout\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertProductPresentInMiniShoppingCart
 * Check that product is present in mini shopping cart
 */
class AssertProductPresentInMiniShoppingCart extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert product is present on mini shopping cart
     *
     * @param CmsIndex $cmsIndex
     * @param array $products
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex, array $products)
    {
        $cmsIndex->open();
        foreach ($products as $product) {
            $cmsIndex->getCartSidebarBlock()->openMiniCart();
            \PHPUnit_Framework_Assert::assertTrue(
                $cmsIndex->getCartSidebarBlock()->getCartItem($product)->isVisible(),
                'Product ' . $product->getName() . ' is absent in Mini Shopping Cart.'
            );
        }
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Products is presents in Mini Shopping Cart.';
    }
}
