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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Tax_Model_Mysql4_Report_Tax extends Mage_Reports_Model_Mysql4_Report_Abstract
{
    protected function _construct()
    {
        $this->_init('tax/tax_order_aggregated_created', 'id');
    }

    /**
     * Aggregate Tax data
     *
     * @param mixed $from
     * @param mixed $to
     * @return Mage_Tax_Model_Mysql4_Tax
     */
    public function aggregate($from = null, $to = null)
    {
        $this->_checkDates($from, $to);
        $writeAdapter = $this->_getWriteAdapter();
        $writeAdapter->beginTransaction();

        try {
             if ($from !== null || $to !== null) {
                $subSelect = $this->_getTableDateRangeSelect(
                    $this->getTable('sales/order'),
                    'created_at', 'updated_at', $from, $to
                );
            } else {
                $subSelect = null;
            }

            $this->_clearTableByDateRange($this->getMainTable(), $from, $to, $subSelect);

            $columns = array(
                'period'                => 'DATE(created_at)',
                'store_id'              => 'store_id',
                'code'                  => 'tax.code',
                'order_status'          => 'e.status',
                'percent'               => 'tax.percent',
                'orders_count'          => 'COUNT(DISTINCT(e.entity_id))',
                'tax_base_amount_sum'   => 'SUM(tax.base_real_amount * e.base_to_global_rate)'
            );

            $select = $writeAdapter->select();
            $select->from(array('e' => $this->getTable('sales/order')), $columns)
                ->joinInner(array('tax' => $this->getTable('tax/sales_order_tax')), 'e.entity_id = tax.order_id', array());

            if ($subSelect !== null) {
                $select->where("DATE(e.created_at) IN(?)", new Zend_Db_Expr($subSelect));
            }

            $select->group(new Zend_Db_Expr('1,2,3'));

            $writeAdapter->query($select->insertFromSelect($this->getMainTable(), array_keys($columns)));

            $select->reset();

            $columns = array(
                'period'                => 'period',
                'store_id'              => new Zend_Db_Expr('0'),
                'code'                  => 'code',
                'order_status'          => 'order_status',
                'percent'               => 'percent',
                'orders_count'          => 'SUM(orders_count)',
                'tax_base_amount_sum'   => 'SUM(tax_base_amount_sum)'
            );

            $select
                ->from($this->getMainTable(), $columns)
                ->where("store_id <> 0");

            if ($subSelect !== null) {
                $select->where("DATE(period) IN(?)", new Zend_Db_Expr($subSelect));
            }

            $select->group(array(
                'period',
                'code',
                'order_status'
            ));

            $writeAdapter->query($select->insertFromSelect($this->getMainTable(), array_keys($columns)));

            $this->_setFlagData(Mage_Reports_Model_Flag::REPORT_TAX_FLAG_CODE);
        } catch (Exception $e) {
            $writeAdapter->rollBack();
            throw $e;
        }

        $writeAdapter->commit();
        return $this;
    }
}
