<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block that renders VDE tools panel
 */
class Mage_DesignEditor_Block_Adminhtml_Editor_Tools extends Mage_Core_Block_Template
{
    /**
     * Get tabs
     *
     * @return array
     */
    public function getTabs()
    {
        return array(
            $this->getChildHtml('design_editor_tools_design'),
            $this->getChildHtml('design_editor_tools_block'),
            $this->getChildHtml('design_editor_tools_settings'),
            $this->getChildHtml('design_editor_tools_code'),
        );
    }
}
