<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\User\Test\Constraint;

use Magento\User\Test\Fixture\User;
use Magento\User\Test\Page\Adminhtml\UserEdit;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertUserInvalidEmailMessage
 */
class AssertUserInvalidEmailMessage extends AbstractConstraint
{
    const ERROR_MESSAGE = 'Please correct this email address: "%s".';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Asserts that error message equals to expected message.
     *
     * @param UserEdit $userEdit
     * @param User $user
     * @return void
     */
    public function processAssert(UserEdit $userEdit, User $user)
    {
        $expectedMessage = sprintf(self::ERROR_MESSAGE, $user->getEmail());
        $actualMessage = $userEdit->getMessagesBlock()->getErrorMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            $expectedMessage,
            $actualMessage,
            'Wrong error message is displayed.'
            . "\nExpected: " . $expectedMessage
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Returns message if equals to expected message.
     *
     * @return string
     */
    public function toString()
    {
        return 'Error message about invalid email on creation user page is correct.';
    }
}
