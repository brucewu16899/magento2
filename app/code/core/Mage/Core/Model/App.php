<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Applcation model
 *
 * Application need have: areas, store, locale, translator, design package
 *
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Core_Model_App
{
    const XML_PATH_INSTALL_DATE = 'global/install/date';

    const DEFAULT_ERROR_HANDLER = 'mageCoreErrorHandler';
    
    const DEFAULT_STORE_CODE    = 'base';

    /**
     * Application loaded areas array
     *
     * @var array
     */
    protected $_areas = array();

    /**
     * Application store object
     *
     * @var Mage_Core_Model_Store
     */
    protected $_store;

    /**
     * Application website object
     *
     * @var Mage_Core_Model_Website
     */
    protected $_website;

    /**
     * Application location object
     *
     * @var Mage_Core_Model_Locale
     */
    protected $_locale;

    /**
     * Application translate object
     *
     * @var Mage_Core_Model_Translate
     */
    protected $_translator;

    /**
     * Application design package object
     *
     * @var Mage_Core_Model_Design_Package
     */
    protected $_design;

    /**
     * Application layout object
     *
     * @var Mage_Core_Model_Layout
     */
    protected $_layout;

    /**
     * Application configuration object
     *
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * Application front controller
     *
     * @var Mage_Core_Controller_Varien_Front
     */
    protected $_frontController;

    /**
     * Cache object
     *
     * @var Zend_Cache_Core
     */
    protected $_cache;

    /**
    * Use Cache
    *
    * @var array
    */
    protected $_useCache;

    public function __construct() {}

    /**
     * Initialize application
     *
     * @param string $store
     * @param string $etcDir
     * @return Mage_Core_Model_App
     */
    public function init($store, $etcDir)
    {
        Varien_Profiler::start('app/construct');

        $this->setErrorHandler(self::DEFAULT_ERROR_HANDLER);
        date_default_timezone_set(Mage_Core_Model_Locale::DEFAULT_TIMEZONE);

        $this->_config  = Mage::getConfig()->init($etcDir);

        $this->_initStore($store);
		Varien_Profiler::stop('app/construct');
		return $this;
    }
    
    /**
     * Initialize application store
     *
     * @param   string $code
     * @return  Mage_Core_Model_App
     */
    protected function _initStore($code)
    {
        $this->_store   = Mage::getSingleton('core/store');
        $this->_website = Mage::getSingleton('core/website');
        
        if ($store = $this->getFrontController()->getRequest()->get('store')) {
            $code = $store;
        }
        
        /**
         * Check store code
         */
        if (!Mage::getConfig()->getNode('stores/'.$code)) {
            $code = self::DEFAULT_STORE_CODE;
        }
        
        if ($this->isInstalled()) {
            $this->_store->loadConfig($code);
            if ($this->_store->getWebsiteId()) {
                $this->_website->loadConfig($this->_store->getWebsiteId());
            }
        }
        else {
            $this->_store->setCode($code);
        }
        return $this;
    }

    /**
     * Initialize application front controller
     *
     * @return Mage_Core_Model_App
     */
    protected function _initFrontController()
    {
        $this->_frontController = new Mage_Core_Controller_Varien_Front();
        Mage::register('controller', $this->_frontController);
        $this->_frontController->init();
        return $this;
    }

    /**
     * Redeclare custom error handler
     *
     * @param   string $handler
     * @return  Mage_Core_Model_App
     */
    public function setErrorHandler($handler)
    {
        set_error_handler($handler);
        return $this;
    }

    /**
     * Loading application area
     *
     * @param   string $code
     * @return  Mage_Core_Model_App
     */
    public function loadArea($code)
    {
        $this->getArea($code)->load();
        return $this;
    }

    /**
     * Loding part of area data
     *
     * @param   string $area
     * @param   string $part
     * @return  Mage_Core_Model_App
     */
    public function loadAreaPart($area, $part)
    {
        $this->getArea($area)->load($part);
        return $this;
    }

    /**
     * Retrieve application area
     *
     * @param   string $code
     * @return  Mage_Core_Model_App_Area
     */
    public function getArea($code)
    {
        if (!isset($this->_areas[$code])) {
            $this->_areas[$code] = new Mage_Core_Model_App_Area($code, $this);
        }
        return $this->_areas[$code];
    }

    /**
     * Retrieve application store object
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return $this->_store;
    }

    /**
     * Retrieve application website object
     *
     * @return Mage_Core_Model_Website
     */
    public function getWebsite()
    {
        return $this->_website;
    }

    /**
     * Retrieve application locale object
     *
     * @return Mage_Core_Model_Locale
     */
    public function getLocale()
    {
        if (!$this->_locale) {
            $this->_locale = Mage::getSingleton('core/locale');
        }
        return $this->_locale;
    }

    /**
     * Retrieve translate object
     *
     * @return Mage_Core_Model_Translate
     */
    public function getTranslator()
    {
        if (!$this->_translator) {
            $this->_translator = Mage::getSingleton('core/translate');
        }
        return $this->_translator;
    }

    /**
     * Retrieve configuration object
     *
     * @return Mage_Core_Model_Config
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Retrieve front controller object
     *
     * @return Mage_Core_Controller_Varien_Front
     */
    public function getFrontController()
    {
        if (!$this->_frontController) {
            $this->_initFrontController();
        }
        return $this->_frontController;
    }

    /**
     * Retrieve application installation flag
     *
     * @return bool
     */
    public function isInstalled()
    {
        $installDate = Mage::getConfig()->getNode(self::XML_PATH_INSTALL_DATE);
        if ($installDate && strtotime($installDate)) {
            return true;
        }
        return false;
    }

    /**
     * Generate cahce id with application specific data
     *
     * @param   string $id
     * @return  string
     */
    protected function _getCacheId($id=null)
    {
        if ($id) {
            $id = strtoupper($id);
        }
        return $id;
    }

    /**
     * Generate cache tags from cache id
     *
     * @param   string $id
     * @param   array $tags
     * @return  array
     */
    protected function _getCacheIdTags($id, $tags=array())
    {
        $idTags = explode('_', $id);

        $first = true;
        foreach ($idTags as $tag) {
            $newTag = $first ? $tag : $newTag . '_' . $tag;
        	if (!in_array($newTag, $tags)) {
        	    $tags[] = $newTag;
        	}
        	$first = false;
        }

        return $tags;
    }

    /**
     * Retrieve cache object
     *
     * @return Zend_Cache_Core
     */
    public function getCache()
    {
        if (!$this->_cache) {
            $this->_cache = Zend_Cache::factory('Core', 'File',
                array('caching'=>true, 'lifetime'=>7200),
                array(
                    'cache_dir'=>Mage::getBaseDir('cache'),
                    'hashed_directory_level'=>1,
                    'hashed_directory_umask'=>0777,
                    'file_name_prefix'=>'mage')
            );
        }
        return $this->_cache;
    }

    /**
     * Loading cache data
     *
     * @param   string $id
     * @return  mixed
     */
    public function loadCache($id)
    {
        return $this->getCache()->load($this->_getCacheId($id));
    }

    /**
     * Saving cache data
     *
     * @param   mixed $data
     * @param   string $id
     * @param   array $tags
     * @return  Mage_Core_Model_App
     */
    public function saveCache($data, $id, $tags=array(), $lifeTime=false)
    {
        $this->getCache()->save($data, $this->_getCacheId($id), $this->_getCacheIdTags($id, $tags), $lifeTime);
        return $this;
    }

    /**
     * Remove cache
     *
     * @param   string $id
     * @return  Mage_Core_Model_App
     */
    public function removeCache($id)
    {
        $this->getCache()->remove($this->_getCacheId($id));
        return $this;
    }

    /**
     * Cleaning cache
     *
     * @param   array $tags
     * @return  Mage_Core_Model_App
     */
    public function cleanCache($tags=array())
    {
        $this->getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, $tags);
        return $this;
    }

    /**
    * Check whether to use cache for specific component
    *
    * Components:
    * - config
    * - layout
    * - eav
    * - translate
    *
    * @return boolean
    */
    public function useCache($type)
    {
        if (!$this->_useCache) {
            $data = $this->getCache()->load('use_cache');
            if (is_string($data)) {
                $this->_useCache = unserialize($data);
            } else {
                $this->_useCache = array();
            }
        }
        return isset($this->_useCache[$type]) ? (bool)$this->_useCache[$type] : false;
    }
}
