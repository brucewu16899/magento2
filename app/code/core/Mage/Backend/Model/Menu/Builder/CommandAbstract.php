<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
abstract class Mage_Backend_Model_Menu_Builder_CommandAbstract
{
    /**
     * Command data
     *
     * @var array
     */
    protected $_data;

    /**
     * @var Mage_Backend_Model_Menu_Builder_CommandAbstract
     */
    protected $_next = null;

    /**
     * Add command as last in the list of callbacks
     *
     * @param Mage_Backend_Model_Menu_Builder_CommandAbstract $command
     * @return Mage_Backend_Model_Menu_Builder_CommandAbstract
     */
    public function chain(Mage_Backend_Model_Menu_Builder_CommandAbstract $command)
    {
        if (is_null($this->_next)) {
            $this->_next = $command;
        } else {
            $this->_next->chain($command);
        }
        return $this;
    }

    /**
     * Execute command actions and pass control to chained commands
     *
     * @param Mage_Backend_Model_Menu_Item $item
     */
    public function execute(Mage_Backend_Model_Menu_Item $item)
    {
        $this->_execute($item);
        if (!is_null($this->_next)) {
            $this->_next->execute($item);
        }
        return $item;
    }

    /**
     * Execute internal command actions
     *
     * @param Mage_Backend_Model_Menu_Item $item
     * @return Mage_Backend_Model_Menu_Item
     */
    protected abstract function _execute(Mage_Backend_Model_Menu_Item $item);
}
