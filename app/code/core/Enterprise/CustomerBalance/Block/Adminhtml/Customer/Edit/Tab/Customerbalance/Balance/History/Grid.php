<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Customer balance history grid
 */
class Enterprise_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_History_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @var Enterprise_CustomerBalance_Model_Resource_Balance_Collection
     */
    protected $_collection;

    /**
     * Initialize some params
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('historyGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('updated_at');
    }

    /**
     * Prepare grid collection
     *
     * @return Enterprise_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_History_Grid
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Enterprise_CustomerBalance_Model_Balance_History')
            ->getCollection()
            ->addFieldToFilter('customer_id', $this->getRequest()->getParam('id'));
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Enterprise_CustomerBalance_Block_Adminhtml_Customer_Edit_Tab_Customerbalance_Balance_History_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('updated_at', array(
            'header'    => Mage::helper('Enterprise_CustomerBalance_Helper_Data')->__('Date'),
            'index'     => 'updated_at',
            'type'      => 'datetime',
            'filter'    => false,
            'width'     => 200,
        ));

        $this->addColumn('website_id', array(
            'header'    => Mage::helper('Enterprise_CustomerBalance_Helper_Data')->__('Website'),
            'index'     => 'website_id',
            'type'      => 'options',
            'options'   => Mage::getSingleton('Mage_Adminhtml_Model_System_Store')->getWebsiteOptionHash(),
            'sortable'  => false,
            'width'     => 200,
        ));

        $this->addColumn('balance_action', array(
            'header'    => Mage::helper('Enterprise_CustomerBalance_Helper_Data')->__('Action'),
            'width'     => 70,
            'index'     => 'action',
            'sortable'  => false,
            'type'      => 'options',
            'options'   => Mage::getSingleton('Enterprise_CustomerBalance_Model_Balance_History')->getActionNamesArray(),
        ));

        $this->addColumn('balance_delta', array(
            'header'    => Mage::helper('Enterprise_CustomerBalance_Helper_Data')->__('Balance Change'),
            'width'     => 50,
            'index'     => 'balance_delta',
            'type'      => 'price',
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'Enterprise_CustomerBalance_Block_Adminhtml_Widget_Grid_Column_Renderer_Currency',
        ));

        $this->addColumn('balance_amount', array(
            'header'    => Mage::helper('Enterprise_CustomerBalance_Helper_Data')->__('Balance'),
            'width'     => 50,
            'index'     => 'balance_amount',
            'sortable'  => false,
            'filter'    => false,
            'renderer'  => 'Enterprise_CustomerBalance_Block_Adminhtml_Widget_Grid_Column_Renderer_Currency',
        ));

        $this->addColumn('is_customer_notified', array(
            'header'    => Mage::helper('Enterprise_CustomerBalance_Helper_Data')->__('Customer notified?'),
            'index'     => 'is_customer_notified',
            'type'      => 'options',
            'options'   => array(
                '1' => Mage::helper('Enterprise_CustomerBalance_Helper_Data')->__('Notified'),
                '0' => Mage::helper('Enterprise_CustomerBalance_Helper_Data')->__('No'),
            ),
            'sortable'  => false,
            'filter'    => false,
            'width'     => 75,
        ));

        $this->addColumn('additional_info', array(
            'header'    => Mage::helper('Enterprise_CustomerBalance_Helper_Data')->__('Additional information'),
            'index'     => 'additional_info',
            'sortable'  => false,
        ));

        return parent::_prepareColumns();
    }

    /**
     * Row click callback
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/gridHistory', array('_current'=> true));
    }
}
