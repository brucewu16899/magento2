<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Core\Test\Constraint;

use Magento\Core\Test\Fixture\SystemVariable;
use Magento\Core\Test\Page\Adminhtml\SystemVariableIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCustomVariableInGrid
 * Check that created custom variable is displayed on backend in custom variable grid and has correct data
 * according to dataset
 */
class AssertCustomVariableInGrid extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert custom variable is displayed on backend in custom variable grid
     *
     * @param SystemVariableIndex $systemVariableIndexNew
     * @param SystemVariable $customVariable
     * @return void
     */
    public function processAssert(
        SystemVariableIndex $systemVariableIndexNew,
        SystemVariable $customVariable
    ) {
        $filter = [
            'code' => $customVariable->getCode(),
            'name' => $customVariable->getName(),
        ];

        $systemVariableIndexNew->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $systemVariableIndexNew->getSystemVariableGrid()->isRowVisible($filter),
            'Custom Variable with code \'' . $filter['code'] . '\' is absent in Custom Variable grid.'
        );
    }

    /**
     * Returns a string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return 'Custom System Variable is present in grid.';
    }
}
