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
 * Adminhtml tags detail for customer report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Tag_Customer_Detail_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('customers_grid');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('Mage_Tag_Model_Tag')
            ->getEntityCollection()
            ->joinAttribute('original_name', 'catalog_product/name', 'entity_id')
            ->addCustomerFilter($this->getRequest()->getParam('id'))
            ->addStatusFilter(Mage_Tag_Model_Tag::STATUS_APPROVED)
            ->setDescOrder('DESC')
            ->addStoresVisibility()
            ->setActiveFilter()
            ->addGroupByTag()
            ->setRelationId();

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Product Name'),
            'sortable'  => false,
            'index'     =>'original_name'
        ));

        $this->addColumn('tag_name', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Tag Name'),
            'sortable'  => false,
            'index'     =>'tag_name'
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('visible', array(
                'header'    => Mage::helper('Mage_Reports_Helper_Data')->__('Visible In'),
                'sortable'  => false,
                'index'     => 'stores',
                'type'      => 'store',
                'store_view'=> true
            ));

            $this->addColumn('added_in', array(
                'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Submitted In'),
                'sortable'  => false,
                'index'     =>'store_id',
                'type'      =>'store',
                'store_view'=>true
            ));
        }

        $this->addColumn('created_at', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Submitted On'),
            'sortable'  => false,
            'width'     => '140px',
            'type'      => 'datetime',
            'index'     => 'created_at'
        ));

        $this->setFilterVisibility(false);

        $this->addExportType('*/*/exportCustomerDetailCsv', Mage::helper('Mage_Reports_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportCustomerDetailExcel', Mage::helper('Mage_Reports_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}
