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
 * Adminhtml order items grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Dmitriy Soroka <dmitriy.soroka@varien.com>
 */
class Mage_Adminhtml_Block_Sales_Order_View_Items extends Mage_Adminhtml_Block_Widget
{
    /**
     * Initialize template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('sales/order/view/items.phtml');
    }

    /**
     * REtrieve order instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('sales_order');
    }

    /**
     * Retrieve order items collection
     *
     * @return unknown
     */
    public function getItemsCollection()
    {
        return $this->getOrder()->getItemsCollection();
    }

    public function renderInfoColumn($item)
    {
        $html = $this->getLayout()->createBlock('adminhtml/sales_order_view_items_info')
            ->setEntity($item)
            ->toHtml();
        return $html;
    }
}
