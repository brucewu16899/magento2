<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Shopping Cart Rules Grid
 *
 * @category Mage
 * @package Mage_Adminhtml
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Promo_Quote_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize grid
     * Set sort settings
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('promo_quote_grid');
        $this->setDefaultSort('sort_order');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * Add websites to sales rules collection
     * Set collection
     *
     * @return Mage_Adminhtml_Block_Promo_Quote_Grid
     */
    protected function _prepareCollection()
    {
        /** @var $collection Mage_SalesRule_Model_Resource_Rule_Collection */
        $collection = Mage::getModel('Mage_SalesRule_Model_Rule')
            ->getResourceCollection();
        $collection->addWebsitesToResult();
        $this->setCollection($collection);

        parent::_prepareCollection();
        return $this;
    }

    /**
     * Add grid columns
     *
     * @return Mage_Adminhtml_Block_Promo_Quote_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('rule_id', array(
            'header'    => Mage::helper('Mage_SalesRule_Helper_Data')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'rule_id',
        ));

        $this->addColumn('name', array(
            'header'    => Mage::helper('Mage_SalesRule_Helper_Data')->__('Rule Name'),
            'align'     =>'left',
            'index'     => 'name',
        ));

        $this->addColumn('coupon_code', array(
            'header'    => Mage::helper('Mage_SalesRule_Helper_Data')->__('Coupon Code'),
            'align'     => 'left',
            'width'     => '150px',
            'index'     => 'code',
        ));

        $this->addColumn('from_date', array(
            'header'    => Mage::helper('Mage_SalesRule_Helper_Data')->__('Date Start'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'index'     => 'from_date',
        ));

        $this->addColumn('to_date', array(
            'header'    => Mage::helper('Mage_SalesRule_Helper_Data')->__('Date Expire'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'to_date',
        ));

        $this->addColumn('is_active', array(
            'header'    => Mage::helper('Mage_SalesRule_Helper_Data')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('rule_website', array(
                'header'    => Mage::helper('salesrule')->__('Website'),
                'align'     =>'left',
                'index'     => 'website_ids',
                'type'      => 'options',
                'sortable'  => false,
                'options'   => Mage::getSingleton('adminhtml/system_store')->getWebsiteOptionHash(),
                'width'     => 200,
            ));
        }

        $this->addColumn('sort_order', array(
            'header'    => Mage::helper('Mage_SalesRule_Helper_Data')->__('Priority'),
            'align'     => 'right',
            'index'     => 'sort_order',
            'width'     => 100,
        ));

        parent::_prepareColumns();
        return $this;
    }

    /**
     * Retrieve row click URL
     *
     * @param Varien_Object $row
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getRuleId()));
    }

}
