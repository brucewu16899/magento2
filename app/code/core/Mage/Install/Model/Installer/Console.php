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
 * Magento application console installer
 */
class Mage_Install_Model_Installer_Console extends Mage_Install_Model_Installer_Abstract
{
    /**
     * Available installation options
     *
     * @var array
     */
    protected $_installOptions = array(
        'license_agreement_accepted' => array('required' => 1),
        'locale'                     => array('required' => 1),
        'timezone'                   => array('required' => 1),
        'default_currency'           => array('required' => 1),
        'db_model'                   => array('required' => 0),
        'db_host'                    => array('required' => 1),
        'db_name'                    => array('required' => 1),
        'db_user'                    => array('required' => 1),
        'db_pass'                    => array('required' => 0),
        'db_prefix'                  => array('required' => 0),
        'url'                        => array('required' => 1),
        'skip_url_validation'        => array('required' => 0),
        'use_rewrites'               => array('required' => 1),
        'use_secure'                 => array('required' => 1),
        'secure_base_url'            => array('required' => 1),
        'use_secure_admin'           => array('required' => 1),
        'admin_lastname'             => array('required' => 1),
        'admin_firstname'            => array('required' => 1),
        'admin_email'                => array('required' => 1),
        'admin_username'             => array('required' => 1),
        'admin_password'             => array('required' => 1),
        'admin_no_form_key'          => array('required' => 0),
        'encryption_key'             => array('required' => 0),
        'session_save'               => array('required' => 0),
        'admin_frontname'            => array('required' => 0),
        'enable_charts'              => array('required' => 0),
        'order_increment_prefix'     => array('required' => 0),
    );

    /**
     * Installer data model to store data between installations steps
     *
     * @var Mage_Install_Model_Installer_Data|Mage_Install_Model_Session
     */
    protected $_dataModel;

    /**
     * Constructor
     */
    public function __construct()
    {
        Mage::init();
        $this->_getInstaller()->setDataModel($this->_getDataModel());
    }

    /**
     * Retrieve validated installation options
     *
     * @param array $options
     * @return array|boolean
     */
    protected function _getInstallOptions(array $options)
    {
        /**
         * Check required options
         */
        foreach ($this->_installOptions as $optionName => $optionInfo) {
            if (isset($optionInfo['required']) && $optionInfo['required'] && !isset($options[$optionName])) {
                $this->addError("ERROR: installation option '$optionName' is required.");
            }
        }

        if ($this->hasErrors()) {
            return false;
        }

        /**
         * Validate license agreement acceptance
         */
        if (!$this->_getFlagValue($options['license_agreement_accepted'])) {
            $this->addError('ERROR: You have to accept Magento license agreement terms and conditions to continue installation.');
            return false;
        }

        $result = array();
        foreach ($this->_installOptions as $optionName => $optionInfo) {
            $result[$optionName] = isset($options[$optionName]) ? $options[$optionName] : '';
        }

        return $result;
    }

    /**
     * Add error
     *
     * @param string $error
     * @return Mage_Install_Model_Installer_Console
     */
    public function addError($error)
    {
        $this->_getDataModel()->addError($error);
        return $this;
    }

    /**
     * Check if there were any errors
     *
     * @return boolean
     */
    public function hasErrors()
    {
        return (count($this->_getDataModel()->getErrors()) > 0);
    }

    /**
     * Get all errors
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->_getDataModel()->getErrors();
    }

    /**
     * Return TRUE for 'yes', 1, 'true' (case insensitive) or FALSE otherwise
     *
     * @param string $value
     * @return boolean
     */
    protected function _getFlagValue($value)
    {
        $res = (1 == $value) || preg_match('/^(yes|y|true)$/i', $value);
        return $res;
    }

    /**
     * Get data model (used to store data between installation steps
     *
     * @return Mage_Install_Model_Installer_Data
     */
    protected function _getDataModel()
    {
        if (is_null($this->_dataModel)) {
            $this->_dataModel = Mage::getModel('Mage_Install_Model_Installer_Data');
        }
        return $this->_dataModel;
    }

    /**
     * Install Magento
     *
     * @param array $options
     * @return string|boolean
     */
    public function install(array $options)
    {
        try {
            $options = $this->_getInstallOptions($options);
            if (!$options) {
                return false;
            }

            /**
             * Check if already installed
             */
            if (Mage::isInstalled()) {
                $this->addError('ERROR: Magento is already installed.');
                return false;
            }

            /**
             * Skip URL validation, if set
             */
            $this->_getDataModel()->setSkipUrlValidation($options['skip_url_validation']);
            $this->_getDataModel()->setSkipBaseUrlValidation($options['skip_url_validation']);

            /**
             * Locale settings
             */
            $this->_getDataModel()->setLocaleData(array(
                'locale'            => $options['locale'],
                'timezone'          => $options['timezone'],
                'currency'          => $options['default_currency'],
            ));

            /**
             * Database and web config
             */
            $this->_getDataModel()->setConfigData(array(
                'db_model'               => $options['db_model'],
                'db_host'                => $options['db_host'],
                'db_name'                => $options['db_name'],
                'db_user'                => $options['db_user'],
                'db_pass'                => $options['db_pass'],
                'db_prefix'              => $options['db_prefix'],
                'use_rewrites'           => $this->_getFlagValue($options['use_rewrites']),
                'use_secure'             => $this->_getFlagValue($options['use_secure']),
                'unsecure_base_url'      => $options['url'],
                'secure_base_url'        => $options['secure_base_url'],
                'use_secure_admin'       => $this->_getFlagValue($options['use_secure_admin']),
                'session_save'           => $this->_checkSessionSave($options['session_save']),
                'admin_frontname'        => $this->_checkAdminFrontname($options['admin_frontname']),
                'admin_no_form_key'      => $this->_getFlagValue($options['admin_no_form_key']),
                'skip_url_validation'    => $this->_getFlagValue($options['skip_url_validation']),
                'enable_charts'          => $this->_getFlagValue($options['enable_charts']),
                'order_increment_prefix' => $options['order_increment_prefix'],
            ));

            /**
             * Primary admin user
             */
            $this->_getDataModel()->setAdminData(array(
                'firstname'         => $options['admin_firstname'],
                'lastname'          => $options['admin_lastname'],
                'email'             => $options['admin_email'],
                'username'          => $options['admin_username'],
                'new_password'      => $options['admin_password'],
            ));

            $installer = $this->_getInstaller();

            /**
             * Install configuration
             */
            $installer->installConfig($this->_getDataModel()->getConfigData()); // TODO fix wizard and simplify this everywhere

            if ($this->hasErrors()) {
                return false;
            }

            /**
             * Reinitialize configuration (to use new config data)
             */
            Mage::app()->cleanCache();
            Mage::getConfig()->reinit();

            /**
             * Install database
             */
            $installer->installDb();

            if ($this->hasErrors()) {
                return false;
            }

            // apply data updates
            Mage_Core_Model_Resource_Setup::applyAllDataUpdates();

            /**
             * Validate entered data for administrator user
             */
            $user = $installer->validateAndPrepareAdministrator($this->_getDataModel()->getAdminData());

            if ($this->hasErrors()) {
                return false;
            }

            /**
             * Prepare encryption key and validate it
             */
            $encryptionKey = empty($options['encryption_key'])
                ? md5(Mage::helper('Mage_Core_Helper_Data')->getRandomString(10))
                : $options['encryption_key'];
            $this->_getDataModel()->setEncryptionKey($encryptionKey);
            $installer->validateEncryptionKey($encryptionKey);

            if ($this->hasErrors()) {
                return false;
            }

            /**
             * Create primary administrator user
             */
            $installer->createAdministrator($user);

            if ($this->hasErrors()) {
                return false;
            }

            /**
             * Save encryption key or create if empty
             */
            $installer->installEnryptionKey($encryptionKey);

            if ($this->hasErrors()) {
                return false;
            }

            /**
             * Installation finish
             */
            $installer->finish();

            if ($this->hasErrors()) {
                return false;
            }

            /**
             * Change directories mode to be writable by apache user
             */
            @chmod('var/cache', 0777);
            @chmod('var/session', 0777);

            return $encryptionKey;

        } catch (Exception $e) {
            $this->addError('ERROR: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Uninstall the application
     *
     * @return bool
     */
    public function uninstall()
    {
        if (!Mage::isInstalled()) {
            return true;
        }
        $dbConfig = Mage::getConfig()->getResourceConnectionConfig(Mage_Core_Model_Resource::DEFAULT_SETUP_RESOURCE);
        if ($dbConfig->model != 'mysql4') {
            $this->addError('Database uninstall is supported for the MySQL only.');
            return false;
        }
        $resourceModel = new Mage_Core_Model_Resource;
        $dbConnection = $resourceModel->getConnection(Mage_Core_Model_Resource::DEFAULT_SETUP_RESOURCE);
        $dbConnection->query("DROP DATABASE `$dbConfig->dbname`");
        $dbConnection->query("CREATE DATABASE `$dbConfig->dbname`");
        unlink(Mage::app()->getConfig()->getOptions()->getEtcDir() . '/local.xml');
        Varien_Io_File::rmdirRecursive(Mage::app()->getConfig()->getOptions()->getCacheDir());
        return true;
    }

    /**
     * Retrieve available locale codes
     *
     * @return array
     */
    public function getAvailableLocales()
    {
        return Mage::app()->getLocale()->getOptionLocales();
    }

    /**
     * Retrieve available currency codes
     *
     * @return array
     */
    public function getAvailableCurrencies()
    {
        return Mage::app()->getLocale()->getOptionCurrencies();
    }

    /**
     * Retrieve available timezone codes
     *
     * @return array
     */
    public function getAvailableTimezones()
    {
        return Mage::app()->getLocale()->getOptionTimezones();
    }

    /**
     * Retrieve available installation options
     *
     * @return array
     */
    public function getAvailableInstallOptions()
    {
        $result = array();
        foreach ($this->_installOptions as $optionName => $optionInfo) {
            $result[$optionName] = ($optionInfo['required'] ? 'required' : 'optional');
        }
        return $result;
    }
}
