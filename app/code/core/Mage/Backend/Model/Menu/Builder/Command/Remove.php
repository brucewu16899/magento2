<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu_Builder_Command_Add extends Mage_Backend_Model_Menu_Builder_CommandAbstract
{
    /**
     * Mark item as removed
     *
     * @param Mage_Backend_Model_Menu_Item $item
     */
    protected function _execute(Mage_Backend_Model_Menu_Item $item)
    {
        $item->setIsRemoved(true);
    }
}
