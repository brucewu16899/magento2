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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml sales order view plane
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Block_Sales_Order_View_Form extends Mage_Core_Block_Template
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('order_plane');
        $this->setTemplate('sales/order/view/form.phtml');
        $this->setTitle(__('Order Information'));
    }

    public function getOrder()
    {
        return Mage::registry('sales_order');
    }

    protected function _initChildren()
    {
        parent::_initChildren();
        $this->setChild( 'items', $this->getLayout()->createBlock( 'adminhtml/sales_order_view_items', 'items.grid' ));
        return $this;
    }

    public function getItemsHtml()
    {
        return $this->getChildHtml('items');
    }

    public function getOrderDateFormatted($format='short')
    {
        $dateFormatted = strftime(Mage::getStoreConfig('general/local/date_format_' . $format), strtotime($this->getOrder()->getCreatedAt()));
        return $dateFormatted;
    }

    public function getOrderStatus()
    {
        return Mage::getModel('sales/order_status')->load($this->getOrder()->getOrderStatusId())->getFrontendLabel();
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getPaymentInfoHtml()
    {
        $methodName = $this->getOrder()->getPayment()->getMethod();
        $html = '';
        $methodConfig = new Varien_Object(Mage::getStoreConfig('payment/' . $this->getOrder()->getPayment()->getMethod(), $this->getOrder()->getStoreId()));
        if ($methodConfig) {
            $className = $methodConfig->getModel();
            $method = Mage::getModel($className);
            if ($method) {
                $html = '<p>'.Mage::getStoreConfig('payment/' . $this->getOrder()->getPayment()->getMethod() . '/title').'</p>';
                $method->setPayment($this->getOrder()->getPayment());
            	$methodBlock = $method->createInfoBlock('payment.method.'.$methodName);
            	if (!empty($methodBlock)) {
            	    $html .= $methodBlock->toHtml();
    	        }
            }
        }
        return $html;
    }

}
