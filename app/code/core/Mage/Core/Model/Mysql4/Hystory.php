<?php
/**
 * Data change hystory model
 *
 * @package    Ecom
 * @subpackage Core
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Core_Model_Mysql4_Hystory extends Mage_Core_Model_Mysql4
{
    protected $_changeTable;
    protected $_changeInfoTable;
    
    public function __construct() 
    {
        $this->_changeTable = $this->_getTableName('core_read', 'data_change');
        $this->_changeInfoTable = $this->_getTableName('core_read', 'data_change_info');
    }
    
    /**
     * Add data changes
     * 
     * $data = array(
     *      [$tableName] => array(
     *          [pk_value]
     *          [type] = 'insert' || 'update' || 'delete'
     *          [before]
     *          [after]
     *      )
     * )
     * 
     * @param string $code
     * @param int $userId
     * @param array $data
     */
    public function addChanges($code, $userId, $data)
    {
        
    }
}