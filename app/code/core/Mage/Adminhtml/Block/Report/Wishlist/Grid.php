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
 * Adminhtml wishlist report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Wishlist_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('wishlistReportGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {

        $collection = Mage::getResourceModel('Mage_Reports_Model_Resource_Wishlist_Product_Collection')
            ->addAttributeToSelect('entity_id')
            ->addAttributeToSelect('name')
            ->addWishlistCount();

        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('ID'),
            'width'     =>'50px',
            'index'     =>'entity_id'
        ));

        $this->addColumn('name', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Name'),
            'index'     =>'name'
        ));

        $this->addColumn('wishlists', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Wishlists'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'wishlists'
        ));

        $this->addColumn('bought_from_wishlists', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Bought from wishlists'),
            'width'     =>'50px',
            'align'     =>'right',
            'sortable'  =>false,
            'index'     =>'bought_from_wishlists'
        ));

        $this->addColumn('w_vs_order', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Wishlist vs. Regular Order'),
            'width'     =>'50px',
            'align'     =>'right',
            'sortable'  =>false,
            'index'     =>'w_vs_order'
        ));

        $this->addColumn('num_deleted', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Number of Times Deleted'),
            'width'     =>'50px',
            'align'     =>'right',
            'sortable'  =>false,
            'index'     =>'num_deleted'
        ));

        $this->addExportType('*/*/exportWishlistCsv', Mage::helper('Mage_Reports_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportWishlistExcel', Mage::helper('Mage_Reports_Helper_Data')->__('Excel XML'));

        $this->setFilterVisibility(false);

        return parent::_prepareColumns();
    }

}

