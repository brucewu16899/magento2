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
 * Tile state resolver interface
 *
 * Class that implements this interface is fully responsible for identifying of correct state of the tile related to it
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface Mage_Launcher_Model_Tile_StateResolver
{
    /**
     * Resolve state
     *
     * @abstract
     * @return int identified state
     */
    public function resolve();

    /**
     * Handle System Configuration change (handle related event) and return new state
     *
     * @abstract
     * @param Mage_Core_Model_Config $config
     * @param string $sectionName
     * @return int result state
     */
    public function handleSystemConfigChange(Mage_Core_Model_Config $config, $sectionName);
}
