<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Validator
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test case for Magento_Validator_Constraint_Property
 */
class Magento_Validator_Constraint_PropertyTest extends PHPUnit_Framework_TestCase
{
    const PROPERTY_NAME = 'test';

    /**
     * @var Magento_Validator_Constraint_Property
     */
    protected $_constraint;

    /**
     * @var Magento_Validator_Interface|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_validatorMock;

    /**
     * Set up
     */
    protected function setUp()
    {
        $this->_validatorMock = $this->getMock('Magento_Validator_Interface');
        $this->_constraint = new Magento_Validator_Constraint_Property($this->_validatorMock, self::PROPERTY_NAME);
    }

    /**
     * Test getId method
     */
    public function testGetId()
    {
        $this->assertEmpty($this->_constraint->getId());
        $id = 'foo';
        $constraint = new Magento_Validator_Constraint_Property($this->_validatorMock, self::PROPERTY_NAME, $id);
        $this->assertEquals($id, $constraint->getId());
    }

    /**
     * Test isValid method
     *
     * @dataProvider isValidDataProvider
     *
     * @param mixed $value
     * @param bool $expectedResult
     * @param array $expectedMessages
     */
    public function testIsValid($value, $validateValue, $expectedResult, $validatorMessages = array(),
        $expectedMessages = array()
    ) {
        $this->_validatorMock
            ->expects($this->once())->method('isValid')
            ->with($validateValue)->will($this->returnValue($expectedResult));

        if ($expectedResult) {
            $this->_validatorMock->expects($this->never())->method('getMessages');
        } else {
            $this->_validatorMock
                ->expects($this->once())->method('getMessages')
                ->will($this->returnValue($validatorMessages));
        }

        $this->assertEquals($expectedResult, $this->_constraint->isValid($value));
        $this->assertEquals($expectedMessages, $this->_constraint->getMessages());
    }

    /**
     * Data provider for testIsValid
     *
     * @return array
     */
    public function isValidDataProvider()
    {
        return array(
            array(
                array(self::PROPERTY_NAME => 'Property value', 'foo' => 'Foo value'),
                'Property value',
                true
            ),
            array(
                new Varien_Object(array(self::PROPERTY_NAME => 'Property value')),
                'Property value',
                true
            ),
            array(
                new ArrayObject(array(self::PROPERTY_NAME => 'Property value')),
                'Property value',
                true
            ),
            array(
                array(self::PROPERTY_NAME => 'Property value', 'foo' => 'Foo value'),
                'Property value',
                false,
                array('Error message 1', 'Error message 2'),
                array(self::PROPERTY_NAME => array('Error message 1', 'Error message 2')),
            ),
            array(
                array('foo' => 'Foo value'),
                null,
                false,
                array('Error message 1'),
                array(self::PROPERTY_NAME => array('Error message 1')),
            ),
            array(
                'scalar',
                null,
                false,
                array('Error message 1'),
                array(self::PROPERTY_NAME => array('Error message 1')),
            )
        );
    }
}
