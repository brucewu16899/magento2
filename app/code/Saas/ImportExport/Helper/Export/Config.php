<?php
/**
 * Saas Export Config Helper
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_ImportExport_Helper_Export_Config extends Mage_Core_Helper_Abstract
{
    /**
     * Default items per page
     */
    const DEFAULT_ITEMS_PER_PAGE = 100;

    /**#@+
     * Config keys
     */
    const XML_PATH_CONFIG_KEY_ENTITIES = 'global/importexport/export_entities/%s/per_page';
    /**#@-*/

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_applicationConfig;

    /**
     * @var Mage_Core_Model_Dir
     */
    protected $_dir;

    /**
     * @param Mage_Core_Helper_Context $context
     * @param Mage_Core_Model_Config $applicationConfig
     * @param Mage_Core_Model_Dir $dir
     */
    public function __construct(
        Mage_Core_Helper_Context $context,
        Mage_Core_Model_Config $applicationConfig,
        Mage_Core_Model_Dir $dir
    ) {
        parent::__construct($context);

        $this->_applicationConfig = $applicationConfig;
        $this->_dir = $dir;
    }

    /**
     * @param string $entityType
     * @return int
     */
    public function getItemsPerPage($entityType)
    {
        $items = (int)$this->_applicationConfig->getNode(sprintf(self::XML_PATH_CONFIG_KEY_ENTITIES, $entityType));
        return $items ? $items : self::DEFAULT_ITEMS_PER_PAGE;
    }

    /**
     * Retrieve path for export file
     *
     * @param string $entityType
     * @return string
     */
    public function getStorageFilePath($entityType)
    {
        return $this->_dir->getDir('media') . DS . 'importexport' . DS . 'export' . DS . $entityType;
    }
}
