<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Mage_Eav
 */
class Mage_Eav_Model_Resource_Entity_Attribute_CollectionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Eav_Model_Resource_Entity_Attribute_Collection
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Eav_Model_Resource_Entity_Attribute_Collection();
    }

    public function testSetAttributeSetExcludeFilter()
    {
        $collection = new Mage_Eav_Model_Resource_Entity_Attribute_Collection();
        $setsPresent = $this->_getSets($collection);
        $excludeSetId = current($setsPresent);

        $this->_model->setAttributeSetExcludeFilter($excludeSetId);
        $sets = $this->_getSets($this->_model);

        $this->assertNotContains($excludeSetId, $sets);
    }

    /**
     * Returns array of set ids, present in collection attributes
     *
     * @param Mage_Eav_Model_Resource_Entity_Attribute_Collection $collection
     * @return array
     */
    protected function _getSets($collection)
    {
        $collection->addSetInfo();

        $sets = array();
        foreach ($collection as $attribute) {
            foreach (array_keys($attribute->getAttributeSetInfo()) as $setId) {
                $sets[$setId] = $setId;
            }
        }
        return array_values($sets);
    }

    public function testSetAttributeGroupFilter()
    {
        $collection = new Mage_Eav_Model_Resource_Entity_Attribute_Collection();
        $groupsPresent = $this->_getGroups($collection);
        $includeGroupId = current($groupsPresent);

        $this->_model->setAttributeGroupFilter($includeGroupId);
        $groups = $this->_getGroups($this->_model);

        $this->assertEquals(array($includeGroupId), $groups);
    }

    /**
     * Returns array of group ids, present in collection attributes
     *
     * @param Mage_Eav_Model_Resource_Entity_Attribute_Collection $collection
     * @return array
     */
    protected function _getGroups($collection)
    {
        $collection->addSetInfo();

        $groups = array();
        foreach ($collection as $attribute) {
            foreach ($attribute->getAttributeSetInfo() as $setInfo) {
                $groupId = $setInfo['group_id'];
                $groups[$groupId] = $groupId;
            }
        }
        return array_values($groups);
    }

    /**
     * @covers Mage_Eav_Model_Resource_Entity_Attribute_Collection::addAttributeGrouping
     */
    public function testAddAttributeGrouping()
    {
        $select = $this->_model->getSelect();
        $select->join(
            array('duplication' => 'eav_entity_attribute'),
            'duplication.attribute_id IN (main_table.attribute_id, main_table.attribute_id + 1)',
            array('unneeded_val' => 'duplication.attribute_id')
        );
        $this->_model->addAttributeGrouping();

        try {
            $this->_model->load();
        } catch (Exception $e) {
            /* Collection threw exception either because duplicated items were loaded, or due to other problem
            with grouping */
            $this->fail('Grouping is not working: ' . $e->getMessage());
        }
    }
}