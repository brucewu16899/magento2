<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Design Drawer Block
 *
 * @category   Mage
 * @package    Mage_Launcher
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer extends Mage_Launcher_Block_Adminhtml_Drawer
{
    /**
     * @var Mage_Core_Model_Theme_Service
     */
    protected $_themeService;

    /**
     * Constructor
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Model_Layout $layout
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Backend_Model_Url $urlBuilder
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_Cache $cache
     * @param Mage_Core_Model_Design_Package $designPackage
     * @param Mage_Core_Model_Session $session
     * @param Mage_Core_Model_Store_Config $storeConfig
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Logger $logger
     * @param Magento_Filesystem $filesystem
     * @param Mage_Launcher_Model_LinkTracker $linkTracker
     * @param Mage_Core_Model_Theme_Service $themeService
     * @param array $data
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Backend_Model_Url $urlBuilder,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Design_Package $designPackage,
        Mage_Core_Model_Session $session,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Logger $logger,
        Magento_Filesystem $filesystem,
        Mage_Launcher_Model_LinkTracker $linkTracker,
        Mage_Core_Model_Theme_Service $themeService,
        array $data = array()
    )
    {
        parent::__construct($request, $layout, $eventManager, $urlBuilder, $translator, $cache, $designPackage,
            $session, $storeConfig, $frontController, $helperFactory, $dirs, $logger, $filesystem, $linkTracker, $data
        );

        $this->_themeService = $themeService;
    }

    /**
     * Get Translated Tile Header
     *
     * @return string
     */
    public function getTileHeader()
    {
        return $this->helper('Mage_Launcher_Helper_Data')->__('Store Design');
    }

    /**
     * Get Themes
     *
     * @return Mage_Core_Model_Resource_Theme_Collection
     */
    public function getThemes()
    {
        return $this->_themeService->getAllThemes();
    }

    /**
     * Retrieve Store Config
     *
     * @param string $path
     * @return mixed
     */
    public function getConfigValue($path)
    {
        return $this->_storeConfig->getConfig($path);
    }

    /**
     * Get current theme_id
     *
     * @return int|null
     */
    public function getCurrentThemeId()
    {
        return $this->getConfigValue('design/theme/theme_id');
    }

    /**
     * Get current theme
     *
     * @return Mage_Core_Model_Theme
     */
    public function getCurrentTheme()
    {
        return $this->_themeService->getThemeById($this->getCurrentThemeId());
    }

    /**
     * Retrieve array of themes blocks
     *
     * @return array|null
     */
    public function getThemesBlocks()
    {
        $themesBlocks = array();
        /** @var $block Mage_Launcher_Block_Adminhtml_Storelauncher_Design_Drawer_Theme */
        $block = $this->getChildBlock('theme-preview');
        foreach($this->getThemes() as $theme) {
            $themeBlock = clone $block;
            $themeBlock->setTheme($theme);
            $themesBlocks[] = $themeBlock;
        }

        return $themesBlocks;
    }

}
