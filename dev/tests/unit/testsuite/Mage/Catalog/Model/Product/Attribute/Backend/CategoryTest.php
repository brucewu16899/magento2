<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Model_Product_Attribute_Backend_CategoryTest extends PHPUnit_Framework_TestCase
{
    public function testAfterLoad()
    {
        $categoryIds = array(1,2,3,4,5);

        $product = $this->getMock('Varien_Object', array('getCategoryIds', 'setData'));
        $product->expects($this->once())
            ->method('getCategoryIds')
            ->will($this->returnValue($categoryIds));

        $product->expects($this->once())
            ->method('setData')
            ->with('category_ids', $categoryIds);

        $categoryAttribute = $this->getMock('Varien_Object', array('getAttributeCode'));
        $categoryAttribute->expects($this->once())
            ->method('getAttributeCode')
            ->will($this->returnValue('category_ids'));

        $model = new Mage_Catalog_Model_Product_Attribute_Backend_Category();
        $model->setAttribute($categoryAttribute);

        $model->afterLoad($product);
    }
}
