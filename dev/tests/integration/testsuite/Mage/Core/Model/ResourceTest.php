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
 * @group module:Mage_Core
 */
class Mage_Core_Model_ResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Resource
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_Core_Model_Resource();
    }

    public function testGetTableName()
    {
        $tablePrefix = 'prefix_';
        $tablePrefixOrig = Mage::getConfig()->getTablePrefix();
        Mage::getConfig()->setNode('global/resources/db/table_prefix', $tablePrefix);

        $tableSuffix = '_suffix';
        $tableNameOrig = 'core_website';
        $tableName = $this->_model->getTableName(array($tableNameOrig, $tableSuffix));
        $this->assertContains($tablePrefix, $tableName);
        $this->assertContains($tableSuffix, $tableName);
        $this->assertContains($tableNameOrig, $tableName);

        Mage::getConfig()->setNode('global/resources/db/table_prefix', $tablePrefixOrig);

        $tableNameOrig = 'core/website';
        $tableName = $this->_model->getTableName($tableNameOrig);

        $this->assertContains('/', $tableName);
    }
}