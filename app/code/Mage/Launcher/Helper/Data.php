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
 * Launcher data helper
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Helper_Data extends Mage_Core_Helper_Data
{
    /**
     * Config path to launcher phase
     */
    const CONFIG_PATH_LAUNCHER_PHASE = 'launcher/store/phase';

    const LAUNCHER_PHASE_STORE_LAUNCHER = 'store_launcher';
    const LAUNCHER_PHASE_PROMOTE_STORE = 'promote_store';

    /**
     * Store Manager
     *
     * @var Mage_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * Core Config Model
     *
     * @var Mage_Core_Model_Config
     */
    protected $_configModel;

    /**
     * @param Mage_Core_Helper_Context $context
     * @param Mage_Core_Model_Config_Modules $config
     * @param Mage_Core_Model_StoreManager $storeManager
     * @param Mage_Core_Model_Config $configModel
     */
    public function __construct(
        Mage_Core_Helper_Context $context,
        Mage_Core_Model_Config_Modules $config,
        Mage_Core_Model_StoreManager $storeManager,
        Mage_Core_Model_Config $configModel
    ) {
        parent::__construct($context, $config);
        $this->_storeManager = $storeManager;
        $this->_configModel = $configModel;
    }

    /**
     * Get Launcher Phase
     *
     * @return string|null
     */
    public function getLauncherPhase()
    {
        $storeData = $this->_configModel->getStoresConfigByPath(self::CONFIG_PATH_LAUNCHER_PHASE);
        if (isset($storeData[$this->getCurrentStoreView()->getStoreId()])) {
            return $storeData[$this->getCurrentStoreView()->getStoreId()];
        }
        return null;
    }

    /**
     * Get current Store
     *
     * @return Mage_Core_Model_Store
     */
    public function getCurrentStoreView()
    {
        $storeView = $this->_storeManager->getDefaultStoreView();
        return $storeView;
    }

    /**
     * Get tmp Url for uploaded Logo
     *
     * @param string $fileName
     * @return string
     */
    public function getTmpLogoUrl($fileName = '')
    {
        return Mage::getBaseUrl('media') . 'tmp/' .  Mage_Backend_Model_Config_Backend_Image_Logo::UPLOAD_DIR
            . '/' . $fileName;
    }

    /**
     * Get tmp path for Logo
     *
     * @param string $name
     * @return string
     */
    public function getTmpLogoPath($name = '')
    {
        $logoDir = Mage::getBaseDir('media') . DIRECTORY_SEPARATOR . 'tmp' . DIRECTORY_SEPARATOR
            . Mage_Backend_Model_Config_Backend_Image_Logo::UPLOAD_DIR;
        if (!empty($name)) {
            $logoDir .= DIRECTORY_SEPARATOR . $name;
        }
        return $logoDir;
    }
}
