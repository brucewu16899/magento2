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
 * Adminhtml products report grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Report_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('productsReportGrid');
        $this->setDefaultSort('entity_id');
        $this->setDefaultDir('desc');
    }

    protected function _prepareCollection()
    {

        $collection = Mage::getResourceModel('Mage_Reports_Model_Resource_Product_Collection');
        $collection->getEntity()->setStore(0);

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _afterLoadCollection()
    {
        $totalObj = new Mage_Reports_Model_Totals();
        $this->setTotals($totalObj->countTotals($this));
    }

    protected function _prepareColumns()
    {
        $this->addColumn('entity_id', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('ID'),
            'width'     =>'50px',
            'index'     =>'entity_id',
            'total'     =>'Total'
        ));

        $this->addColumn('name', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Name'),
            'index'     =>'name'
        ));

        $this->addColumn('viewed', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Number Viewed'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'viewed',
            'total'     =>'sum'
        ));

        $this->addColumn('added', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Number Added'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'added',
            'total'     =>'sum'
        ));

        $this->addColumn('purchased', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Number Purchased'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'purchased',
            'total'     =>'sum'
        ));

        $this->addColumn('fulfilled', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Number Fulfilled'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'fulfilled',
            'total'     =>'sum'
        ));

        $this->addColumn('revenue', array(
            'header'    =>Mage::helper('Mage_Reports_Helper_Data')->__('Revenue'),
            'width'     =>'50px',
            'align'     =>'right',
            'index'     =>'revenue',
            'total'     =>'sum'
        ));

        $this->setCountTotals(true);

        $this->addExportType('*/*/exportProductsCsv', Mage::helper('Mage_Reports_Helper_Data')->__('CSV'));
        $this->addExportType('*/*/exportProductsExcel', Mage::helper('Mage_Reports_Helper_Data')->__('Excel XML'));

        return parent::_prepareColumns();
    }

}

