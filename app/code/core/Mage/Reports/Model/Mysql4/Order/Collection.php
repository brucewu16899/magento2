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
 * @package    Mage_Reports
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Reports orders collection
 *
 * @category   Mage
 * @package    Mage_Reports
 * @author     Ivan Chepurnyi  <mitch@varien.com>
 */
class Mage_Reports_Model_Mysql4_Order_Collection extends Mage_Sales_Model_Entity_Order_Collection
{

    public function prepareSummary($range, $customStart, $customEnd, $isFilter=0)
    {

        if ($isFilter==0) {
            $this->addExpressionAttributeToSelect('revenue',
                'SUM({{grand_total}}*{{store_to_base_rate}}/{{store_to_order_rate}})',
                array('grand_total', 'store_to_base_rate', 'store_to_order_rate'));
        } else{
            $this->addExpressionAttributeToSelect('revenue',
                'SUM({{grand_total}}/{{store_to_order_rate}})',
                array('grand_total', 'store_to_order_rate'));
        }

        $this->addExpressionAttributeToSelect('quantity', 'COUNT({{attribute}})', 'entity_id')
            ->addExpressionAttributeToSelect('range', $this->_getRangeExpression($range), 'created_at')
            ->addAttributeToFilter('created_at', $this->_getDateRange($range, $customStart, $customEnd))
            ->groupByAttribute('range')
            ->getSelect()->order('range', 'asc');

        return $this;
    }

    protected function _getRangeExpression($range)
    {
        // dont need of this offset bc we are format date in block
        //$timeZoneOffset = Mage::getModel('core/date')->getGmtOffset();

        switch ($range)
        {
            case '24h':
                $expression = 'DATE_FORMAT({{attribute}}, \'%Y-%m-%d %H:00\')';

                break;
            case '7d':
            case '1m':
               $expression = 'DATE_FORMAT({{attribute}}, \'%Y-%m-%d\')';
               break;
            case '1y':
            case '2y':
            case 'custom':
            default:
                $expression = 'DATE_FORMAT({{attribute}}, \'%Y-%m-01\')';
                break;
        }

        return $expression;
    }

    protected function _getDateRange($range, $customStart, $customEnd)
    {
        $dateEnd = Mage::app()->getLocale()->date();
        $dateStart = clone $dateEnd;

        // go to the end of a day
        $dateEnd->setHour(23);
        $dateEnd->setMinute(59);
        $dateEnd->setSecond(59);

        $dateStart->setHour(0);
        $dateStart->setMinute(0);
        $dateStart->setSecond(0);

        switch ($range)
        {
            case '24h':
                $dateEnd->setHour(date('H'));
                $dateEnd->setMinute(date('i'));
                $dateEnd->setSecond(date('s'));
                $dateStart->setHour(date('H'));
                $dateStart->setMinute(date('i'));
                $dateStart->setSecond(date('s'));
                $dateStart->subDay(1);
                break;

            case '7d':
                // substract 6 days we need to include
                // only today and not hte last one from range
                $dateStart->subDay(6);
                break;

            case '1m':
                $dateStart->setDay(1);
                break;

            case 'custom':
                $dateStart = $customStart ? $customStart : $dateEnd;
                $dateEnd   = $customEnd ? $customEnd : $dateEnd;
                break;

            case '1y':
                $dateStart->subYear(1);
                break;
            case '2y':
                $dateStart->subYear(2);
                break;
            default:
                $dateStart->subYear(1);
                break;
        }

        return array('from'=>$dateStart, 'to'=>$dateEnd, 'datetime'=>true);
    }

    public function addItemCountExpr()
    {
        $orderTable = $this->getEntity()->getEntityTable();
        $orderItemEntityTypeId = Mage::getResourceSingleton('sales/order_item')->getTypeId();
        $this->getSelect()->join(
                array('items'=>$orderTable),
                'items.parent_id=e.entity_id and items.entity_type_id='.$orderItemEntityTypeId,
                array('items_count'=>new Zend_Db_Expr('COUNT(items.entity_id)'))
            )
            ->group('e.entity_id');
        return $this;
    }

    public function calculateTotals($isFilter = 0)
    {
        if ($isFilter == 0) {
            $this->addExpressionAttributeToSelect(
                    'revenue',
                     'SUM(({{subtotal}}-ifnull({{discount_amount}},0)-ifnull({{total_refunded}},0)-ifnull({{total_canceled}},0))*{{store_to_base_rate}}/{{store_to_order_rate}})',
                     array('subtotal', 'discount_amount', 'store_to_base_rate', 'store_to_order_rate', 'total_refunded', 'total_canceled'))
                ->addExpressionAttributeToSelect(
                    'tax',
                    'SUM({{tax_amount}}*{{store_to_base_rate}}/{{store_to_order_rate}})',
                    array('tax_amount', 'store_to_base_rate', 'store_to_order_rate'))
                ->addExpressionAttributeToSelect(
                    'shipping',
                    'SUM({{shipping_amount}}*{{store_to_base_rate}}/{{store_to_order_rate}})',
                    array('shipping_amount', 'store_to_base_rate', 'store_to_order_rate'))
                ->addExpressionAttributeToSelect(
                    'quantity',
                    'COUNT({{entity_id}}*{{store_to_base_rate}}/{{store_to_order_rate}})',
                    array('entity_id', 'store_to_base_rate', 'store_to_order_rate'));
        } else {
            $this->addExpressionAttributeToSelect(
                    'revenue',
                     'SUM(({{subtotal}}-ifnull({{discount_amount}},0)-ifnull({{total_refunded}},0)-ifnull({{total_canceled}},0))/{{store_to_order_rate}})',
                     array('subtotal', 'discount_amount', 'store_to_order_rate', 'total_refunded', 'total_canceled'))
                ->addExpressionAttributeToSelect(
                    'tax',
                    'SUM({{tax_amount}}/{{store_to_order_rate}})',
                    array('tax_amount', 'store_to_order_rate'))
                ->addExpressionAttributeToSelect(
                    'shipping',
                    'SUM({{shipping_amount}}/{{store_to_order_rate}})',
                    array('shipping_amount', 'store_to_order_rate'))
                ->addExpressionAttributeToSelect(
                    'quantity',
                    'COUNT({{entity_id}}/{{store_to_order_rate}})',
                    array('entity_id', 'store_to_order_rate'));
        }

        $this->groupByAttribute('entity_type_id');
        return $this;
    }

    public function calculateSales($isFilter = 0)
    {
        if ($isFilter == 0) {
            $this->addExpressionAttributeToSelect(
                    'lifetime',
                     'SUM(({{subtotal}}-ifnull({{discount_amount}},0)-ifnull({{total_refunded}},0)-ifnull({{total_canceled}},0))*{{store_to_base_rate}}/{{store_to_order_rate}})',
                     array('subtotal', 'discount_amount', 'store_to_base_rate', 'store_to_order_rate', 'total_refunded', 'total_canceled'))
                ->addExpressionAttributeToSelect(
                    'average',
                    'AVG(({{subtotal}}-ifnull({{discount_amount}},0)-ifnull({{total_refunded}},0)-ifnull({{total_canceled}},0))*{{store_to_base_rate}}/{{store_to_order_rate}})',
                    array('subtotal', 'discount_amount', 'store_to_base_rate', 'store_to_order_rate', 'total_refunded', 'total_canceled'));
        } else {
            $this->addExpressionAttributeToSelect(
                    'lifetime',
                     'SUM(({{subtotal}}-ifnull({{discount_amount}},0)-ifnull({{total_refunded}},0)-ifnull({{total_canceled}},0))/{{store_to_order_rate}})',
                     array('subtotal', 'discount_amount', 'store_to_order_rate', 'total_refunded', 'total_canceled'))
                ->addExpressionAttributeToSelect(
                    'average',
                    'AVG(({{subtotal}}-ifnull({{discount_amount}},0)-ifnull({{total_refunded}},0)-ifnull({{total_canceled}},0))/{{store_to_order_rate}})',
                    array('subtotal', 'discount_amount', 'store_to_order_rate', 'total_refunded', 'total_canceled'));
        }

        $this->groupByAttribute('entity_type_id');
        return $this;
    }

    public function setDateRange($from, $to)
    {
        $this->_reset()
            ->addAttributeToSelect('*')
            ->addAttributeToFilter('created_at', array('from' => $from, 'to' => $to))
            ->addExpressionAttributeToSelect('orders', 'COUNT(DISTINCT({{entity_id}}))', array('entity_id'))
            ->getSelect()->group('("*")');

        /**
         * getting qty count for each order
         */

        $orderItem = Mage::getResourceSingleton('sales/order_item');
        /* @var $orderItem Mage_Sales_Model_Entity_Quote */
        $attr = $orderItem->getAttribute('parent_id');
        /* @var $attr Mage_Eav_Model_Entity_Attribute_Abstract */
        $attrId = $attr->getAttributeId();
        $tableName = $attr->getBackend()->getTable();

        $this->getSelect()
            ->joinLeft(array("order_items" => $tableName),
                "order_items.parent_id = e.entity_id and order_items.entity_type_id=".$orderItem->getTypeId(), array());

        $attr = $orderItem->getAttribute('qty_ordered');
        /* @var $attr Mage_Eav_Model_Entity_Attribute_Abstract */
        $attrId = $attr->getAttributeId();
        $tableName = $attr->getBackend()->getTable();
        $fieldName = $attr->getBackend()->isStatic() ? 'qty_ordered' : 'value';

        $this->getSelect()
            ->joinLeft(array("order_items2" => $tableName),
                "order_items2.entity_id = `order_items`.entity_id and order_items2.attribute_id = {$attrId}", array())
            ->from("", array("items" => "sum(order_items2.{$fieldName})"));

        return $this;
    }

    public function setStoreIds($storeIds)
    {
        $vals = array_values($storeIds);
        if (count($storeIds) >= 1 && $vals[0] != '') {
            $this->addAttributeToFilter('store_id', array('in' => (array)$storeIds))
                ->addExpressionAttributeToSelect(
                    'subtotal',
                    'IFNULL(SUM({{subtotal}}/{{store_to_order_rate}}), 0)',
                    array('subtotal', 'store_to_order_rate'))
                ->addExpressionAttributeToSelect(
                    'tax',
                    'IFNULL(SUM({{tax_amount}}/{{store_to_order_rate}}), 0)',
                    array('tax_amount', 'store_to_order_rate'))
                ->addExpressionAttributeToSelect(
                    'shipping',
                    'IFNULL(SUM({{shipping_amount}}/{{store_to_order_rate}}), 0)',
                    array('shipping_amount', 'store_to_order_rate'))
                ->addExpressionAttributeToSelect(
                    'discount',
                    'IFNULL(SUM({{discount_amount}}/{{store_to_order_rate}}), 0)',
                    array('discount_amount', 'store_to_order_rate'))
                ->addExpressionAttributeToSelect(
                    'total',
                    'IFNULL(SUM({{grand_total}}/{{store_to_order_rate}}), 0)',
                    array('grand_total', 'store_to_order_rate'))
                ->addExpressionAttributeToSelect(
                    'invoiced',
                    'IFNULL(SUM({{total_paid}}/{{store_to_order_rate}}), 0)',
                    array('total_paid', 'store_to_order_rate'))
                ->addExpressionAttributeToSelect(
                    'refunded',
                    'IFNULL(SUM({{total_refunded}}/{{store_to_order_rate}}), 0)',
                    array('total_refunded', 'store_to_order_rate'));
        } else {
            $this->addExpressionAttributeToSelect(
                    'subtotal',
                    'IFNULL(SUM({{store_to_base_rate}}*{{subtotal}}/{{store_to_order_rate}}), 0)',
                    array('subtotal', 'store_to_order_rate', 'store_to_base_rate'))
                ->addExpressionAttributeToSelect(
                    'tax',
                    'IFNULL(SUM({{store_to_base_rate}}*{{tax_amount}}/{{store_to_order_rate}}), 0)',
                    array('tax_amount', 'store_to_order_rate', 'store_to_base_rate'))
                ->addExpressionAttributeToSelect(
                    'shipping',
                    'IFNULL(SUM({{store_to_base_rate}}*{{shipping_amount}}/{{store_to_order_rate}}), 0)',
                    array('shipping_amount', 'store_to_order_rate', 'store_to_base_rate'))
                ->addExpressionAttributeToSelect(
                    'discount',
                    'IFNULL(SUM({{store_to_base_rate}}*{{discount_amount}}/{{store_to_order_rate}}), 0)',
                    array('discount_amount', 'store_to_order_rate', 'store_to_base_rate'))
                ->addExpressionAttributeToSelect(
                    'total',
                    'IFNULL(SUM({{store_to_base_rate}}*{{grand_total}}/{{store_to_order_rate}}), 0)',
                    array('grand_total', 'store_to_order_rate', 'store_to_base_rate'))
                ->addExpressionAttributeToSelect(
                    'invoiced',
                    'IFNULL(SUM({{store_to_base_rate}}*{{total_paid}}/{{store_to_order_rate}}), 0)',
                    array('total_paid', 'store_to_order_rate', 'store_to_base_rate'))
                ->addExpressionAttributeToSelect(
                    'refunded',
                    'IFNULL(SUM({{store_to_base_rate}}*{{total_refunded}}/{{store_to_order_rate}}), 0)',
                    array('total_refunded', 'store_to_order_rate', 'store_to_base_rate'));
        }

        return $this;
    }
}
