<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Theme Service model
 */
class Mage_Core_Model_Theme_Service
{
    /**
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * @var Mage_Core_Model_Design_Package
     */
    protected $_design;

    /**
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * Whether is present customized themes
     *
     * @var bool
     */
    protected $_hasCustomizedThemes;

    /**
     * Customized themes which are assigned to store views or as default
     *
     * @var array
     */
    protected $_assignedThemes;

    /**
     * Customized themes which are not assigned to store views or as default
     *
     * @var array
     */
    protected $_unassignedThemes;

    /**
     * Initialize service model
     *
     * @param Mage_Core_Model_Theme $theme
     * @param Mage_Core_Model_Design_Package $design
     * @param Mage_Core_Model_App $app
     */
    public function __construct(
        Mage_Core_Model_Theme $theme,
        Mage_Core_Model_Design_Package $design,
        Mage_Core_Model_App $app
    ) {
        $this->_theme = $theme;
        $this->_design = $design;
        $this->_app = $app;
    }

    /**
     * @return Mage_Core_Model_Theme_Service
     */
    protected function _createPhysicalThemeCopy()
    {
        if ($this->_theme->isVirtual()) {
            return $this;
        }
        $this->_theme->setParentId($this->_theme->getId())->setThemePath(null)
            ->setThemeTitle($this->_theme->getThemeTitle() . ' - Copy');
        $originalData = $this->_theme->getData();
        unset($originalData[$this->_theme->getIdFieldName()]);
        $this->_theme = clone $this->_theme;
        $this->_theme->addData($originalData);
        $this->_theme->save();
        return $this;
    }

    /**
     * Assign theme to the stores
     *
     * @param int $themeId
     * @param array $stores
     * @param string $scope
     * @param string $area
     * @return Mage_Core_Model_Theme_Service
     * @throws UnexpectedValueException
     */
    public function assignThemeToStores($themeId, $stores = array(), $scope = Mage_Core_Model_Config::SCOPE_STORES,
        $area = Mage_Core_Model_App_Area::AREA_FRONTEND
    ) {
        if (!$this->_theme->load($themeId)->getId()) {
            throw new UnexpectedValueException('Theme is not recognized. Requested id: ' . $themeId);
        }
        $this->_createPhysicalThemeCopy();
        $configPath = $this->_design->getConfigPathByArea($area);
        foreach ($stores as $storeId) {
            $this->_app->getConfig()->saveConfig($configPath, $this->_theme->getId(), $scope, $storeId);
        }
        return $this;
    }

    /**
     * Check whether is present customized themes
     *
     * @return bool
     */
    public function isPresentCustomizedThemes()
    {
        if (is_null($this->_hasCustomizedThemes)) {
            $this->_hasCustomizedThemes = false;
            /** @var $theme Mage_Core_Model_Theme */
            foreach ($this->_theme->getCollection() as $theme) {
                if ($theme->isVirtual()) {
                    $this->_hasCustomizedThemes = true;
                    break;
                }
            }
        }
        return $this->_hasCustomizedThemes;
    }

    /**
     * Return not customized theme collection by page
     *
     * @param int $page
     * @param int $pageSize
     * @return Mage_Core_Model_Resource_Theme_Collection
     */
    public function getNotCustomizedFrontThemes($page, $pageSize)
    {
        /** @var $collection Mage_Core_Model_Resource_Theme_Collection */
        $collection = $this->_theme->getCollection();
        $collection->addAreaFilter(Mage_Core_Model_App_Area::AREA_FRONTEND)
            ->addFilter('theme_path', 'theme_path IS NOT NULL', 'string')
            ->setPageSize($pageSize);
        return $collection->setCurPage($page);
    }

    /**
     * Return customized themes which are assigned to store views or as default
     *
     * @return array
     */
    public function getAssignedThemes()
    {
        if (is_null($this->_assignedThemes)) {
            $this->_prepareCustomizedThemes();
        }
        return $this->_assignedThemes;
    }

    /**
     * Return customized themes which are not assigned to store views or as default
     *
     * @return array
     */
    public function getUnassignedThemes()
    {
        if (is_null($this->_unassignedThemes)) {
            $this->_prepareCustomizedThemes();
        }
        return $this->_unassignedThemes;
    }

    /**
     * Check customized themes and select assigned and unassigned
     *
     * @return Mage_Core_Model_Theme_Service
     */
    protected function _prepareCustomizedThemes()
    {
        /** @var $customizedThemes Mage_Core_Model_Resource_Theme_Collection */
        $customizedThemes = $this->_getCustomizedFrontThemes();
        $assignedThemes = $this->_getStoresByThemes();

        $this->_assignedThemes = array();
        $this->_unassignedThemes = array();
        /** @var $theme Mage_Core_Model_Theme */
        foreach ($customizedThemes as $theme) {
            if (isset($assignedThemes[$theme->getId()])) {
                $theme->setAssignedStores($assignedThemes[$theme->getId()]);
                $this->_assignedThemes[] = $theme;
            } else {
                $this->_unassignedThemes[] = $theme;
            }
        }
        return $this;
    }

    /**
     * Return  customized theme collection
     *
     * @return Mage_Core_Model_Resource_Theme_Collection
     */
    protected function _getCustomizedFrontThemes()
    {
        /** @var $collection Mage_Core_Model_Resource_Theme_Collection */
        $collection = $this->_theme->getCollection();
        $collection->addAreaFilter(Mage_Core_Model_App_Area::AREA_FRONTEND)
            ->addFilter('theme_path', 'theme_path IS NULL', 'string');
        return $collection;
    }

    /**
     * Return stores grouped by assigned themes
     *
     * @return array
     */
    protected function _getStoresByThemes()
    {
        $assignedTheme = array();
        $stores = $this->_app->getStores();
        foreach ($stores as $store) {
            $themeId = $store->getConfig(Mage_Core_Model_Design_Package::XML_PATH_THEME_ID, $store);
            if (!isset($assignedTheme[$themeId])) {
                $assignedTheme[$themeId] = array();
            }
            $assignedTheme[$themeId][] = $store;
        }
        return $assignedTheme;
    }
}
