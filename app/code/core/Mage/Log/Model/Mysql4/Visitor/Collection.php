<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Log
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Mage_Log_Model_Mysql4_Customers_Collection
 *
 * @category   Mage
 * @package    Mage_Log
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
     * Visitor data info table name
     *
     * @var string
     */
    protected $_visitorInfoTable;

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
     * Quote data table.
     *
     * @var string
     */
    protected $_quoteTable;

    /**
     * Construct
     *
     */
    function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        parent::__construct($resource->getConnection('log_read'));

        $this->_visitorTable = $resource->getTableName('log/visitor');
        $this->_visitorInfoTable = $resource->getTableName('log/visitor_info');
        $this->_urlTable = $resource->getTableName('log/url_table');
        $this->_urlInfoTable = $resource->getTableName('log/url_info_table');
        $this->_customerTable = $resource->getTableName('log/customer');
        $this->_summaryTable = $resource->getTableName('log/summary_table');
        $this->_summaryTypeTable = $resource->getTableName('log/summary_type_table');
        $this->_quoteTable = $resource->getTableName('log/quote_table');

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
        $this->_sqlSelect->from(array('visitor_table'=>$this->_visitorTable))
            //->joinLeft(array('url_table'=>$this->_urlTable), 'visitor_table.last_url_id=url_table.url_id')
            ->joinLeft(array('info_table'=>$this->_visitorInfoTable), 'info_table.visitor_id=visitor_table.visitor_id')
            ->joinLeft(array('customer_table'=>$this->_customerTable), 
                'customer_table.visitor_id = visitor_table.visitor_id AND customer_table.logout_at IS NULL')
            ->joinLeft(array('url_info_table'=>$this->_urlInfoTable), 
                'url_info_table.url_id = visitor_table.last_url_id')
            //->joinLeft(array('quote_table'=>$this->_quoteTable), 'quote_table.visitor_id=visitor_table.visitor_id')
            ->where( 'visitor_table.last_visit_at >= ( ? - INTERVAL '.$minutes.' MINUTE)', now() );
        return $this;
    }

    public function showCustomersOnly()
    {
        $this->_sqlSelect->where('customer_table.customer_id > 0')
            ->group('customer_table.customer_id');
        return $this;
    }

    public function getAggregatedData($period=720, $type_id=null)
    {
    	$this->_sqlSelect->from($this->_summaryTable)
    	   ->where( "{$this->_summaryTable}.add_date >= ( ? - INTERVAL {$period} MINUTE)", now() );
    	if( is_null($type_id) ) {
    		$this->_sqlSelect->where("{$this->_summaryTable}.type_id IS NULL");
    	} else {
			$this->_sqlSelect->where("{$this->_summaryTable}.type_id = ? ", $type_id);
    	}
    	return $this;
    }

    public function addFieldToFilter($fieldName, $fieldValue)
    {
        if( $fieldName == 'type' ) {
            if ($fieldValue == 'v') {
            	parent::addFieldToFilter('customer_id', array('null' => 1));
            } else {
                parent::addFieldToFilter('customer_id', array('moreq' => 1));
            }
        }
    }
}