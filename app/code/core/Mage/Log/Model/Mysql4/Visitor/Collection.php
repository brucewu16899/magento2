<?php
/**
 * Mage_Log_Model_Mysql4_Customers_Collection
 *
 * @package     package
 * @subpackage  subpackage
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Log_Model_Mysql4_Visitor_Collection extends Varien_Data_Collection_Db
{
    /**
     * Visitor data table name
     *
     * @var string
     */
    protected $_visitorTable;

    /**
     * Customer data table
     *
     * @var string
     */
    protected $_customerTable;

    /**
     * Log URL data table name.
     *
     * @var string
     */
    protected $_urlTable;

    /**
     * Log URL expanded data table name.
     *
     * @var string
     */
    protected $_urlInfoTable;

    /**
     * Aggregator data table.
     *
     * @var string
     */
    protected $_summaryTable;

    /**
     * Aggregator type data table.
     *
     * @var string
     */
    protected $_summaryTypeTable;

    /**
     * Construct
     *
     */
    function __construct()
    {
        parent::__construct(Mage::getSingleton('core/resource')->getConnection('log_read'));

        $this->_visitorTable = Mage::getSingleton('core/resource')->getTableName('log/visitor');
        $this->_urlTable = Mage::getSingleton('core/resource')->getTableName('log/url_table');
        $this->_urlInfoTable = Mage::getSingleton('core/resource')->getTableName('log/url_info_table');
        $this->_customerTable = Mage::getSingleton('core/resource')->getTableName('log/customer');
        $this->_summaryTable = Mage::getSingleton('core/resource')->getTableName('log/summary_table');
        $this->_summaryTypeTable = Mage::getSingleton('core/resource')->getTableName('log/summary_type_table');

        $this->setItemObjectClass(Mage::getConfig()->getModelClassName('log/visitor'));
    }

    /**
     * Enables online only select
     *
     * @param int $minutes
     * @return object
     */
    public function useOnlineFilter($minutes=15)
    {
        $this->_sqlSelect->from($this->_urlTable);
        $this->_sqlSelect->joinLeft( $this->_visitorTable, "{$this->_visitorTable}.visitor_id = {$this->_urlTable}.visitor_id" );
        $this->_sqlSelect->joinLeft( $this->_customerTable, "{$this->_customerTable}.visitor_id = {$this->_urlTable}.visitor_id AND {$this->_customerTable}.logout_at IS NULL" );
        $this->_sqlSelect->joinLeft( $this->_urlInfoTable, "{$this->_urlInfoTable}.url_id = {$this->_urlTable}.url_id" );
        $this->_sqlSelect->where( new Zend_Db_Expr("{$this->_urlTable}.visit_time >= (NOW() - INTERVAL {$minutes} MINUTE)") );
        $this->_sqlSelect->group("{$this->_urlTable}.visitor_id");
        return $this;
    }
}