<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Abstract resource data model
 *
 * @category    Mage
 * @package     Mage_Install
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Install_Model_Installer_Db_Abstract
{
    /**
     *  Adapter instance
     *
     * @var Varien_Db_Adapter_Interface
     */
    protected $_connection;

    /**
     *  Connection configuration
     *
     * @var array
     */
    protected $_connectionData;

    /**
     *  Connection configuration
     *
     * @var array
     */
    protected $_configData;


    /**
     * Return the name of DB model from config
     *
     * @return string
     */
    public function getModel()
    {
        return $this->_configData['db_model'];
    }


    /**
     * Return the DB type from config
     *
     * @return string
     */
    public function getType()
    {
        return $this->_configData['db_type'];
    }

    /**
     * Set configuration data
     *
     * @param array $config the connection configuration
     */
    public function setConfig($config)
    {
        $this->_configData = $config;
    }

    /**
     * Retrieve connection data from config
     *
     * @return array
     */
    public function getConnectionData()
    {
        if (!$this->_connectionData) {
            $connectionData = array(
                'host'      => $this->_configData['db_host'],
                'username'  => $this->_configData['db_user'],
                'password'  => $this->_configData['db_pass'],
                'dbname'    => $this->_configData['db_name'],
                'pdoType'   => $this->getPdoType()
            );
            $this->_connectionData = $connectionData;
        }
        return $this->_connectionData;
    }

    /**
     * Check InnoDB support
     *
     * @return bool
     */
    public function supportEngine()
    {
        return true;
    }

    /**
     * Create new connection with custom config
     *
     * @return Varien_Db_Adapter_Interface
     */
    protected function _getConnection()
    {
        if (!isset($this->_connection)) {
            $resource   = Mage::getSingleton('Mage_Core_Model_Resource');
            $connection = $resource->createConnection('install', $this->getType(), $this->getConnectionData());
            $this->_connection = $connection;
        }
        return $this->_connection;
    }

    /**
     * Return pdo type
     *
     * @return null
     */
    public function getPdoType()
    {
        return null;
    }

    /**
     * Retrieve required PHP extension list for database
     *
     * @return array
     */
    public function getRequiredExtensions()
    {
        $extensions = array();
        $configExt = (array)Mage::getConfig()->getNode(sprintf('install/databases/%s/extensions', $this->getModel()));
        foreach ($configExt as $name=>$value) {
            $extensions[] = $name;
        }
        return $extensions;
    }
}
