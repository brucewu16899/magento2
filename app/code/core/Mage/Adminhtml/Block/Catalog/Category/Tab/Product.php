<?php
/**
 * Product in category grid
 *
 * @package     MAge
 * @subpackage  Adminhmtl
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Category_Tab_Product extends Mage_Adminhtml_Block_Widget_Grid 
{
    public function __construct() 
    {
        parent::__construct();
        $this->setId('catalog_category_products');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }
    
    /*protected function _initChildren()
    {
        parent::_initChildren();
        $this->setChild('switch_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Edit Sorting'),
                    'onclick'   => ''
                ))
        );
    }

    public function getMainButtonsHtml()
    {
        $html = $this->getChildHtml('switch_button');
        $html.= parent::getMainButtonsHtml();
        return $html;
    }*/

    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in category flag
        if ($column->getId() == 'in_category') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
            	$this->getCollection()->addFieldToFilter('entity_id', array('in'=>$productIds));
            }
            else {
                $this->getCollection()->addFieldToFilter('entity_id', array('nin'=>$productIds));
            }
        }
        else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
    
    protected function _prepareCollection()
    {
        $this->setDefaultFilter(array('in_category'=>1));
        $collection = Mage::getResourceModel('catalog/product_collection')
            ->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->joinField('store_id', 
                'catalog/product_store', 
                'store_id', 
                'product_id=entity_id', 
                '{{table}}.store_id='.(int) $this->getRequest()->getParam('store', 0))
            ->joinField('position', 
                'catalog/category_product', 
                'position', 
                'product_id=entity_id', 
                'category_id='.(int) $this->getRequest()->getParam('id', 0), 
                'left');
        $collection->getEntity()->setStore(0);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns()
    {
        $this->addColumn('in_category', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_category',
            'values'    => $this->_getSelectedProducts(),
            'align'     => 'center',
            'index'     => 'entity_id'
        ));
        $this->addColumn('id', array(
            'header'    => __('ID'),
            'sortable'  => true,
            'width'     => '60px',
            'index'     => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header'    => __('Name'),
            'index'     => 'name'
        ));
        $this->addColumn('sku', array(
            'header'    => __('SKU'),
            'width'     => '80px',
            'index'     => 'sku'
        ));
        $this->addColumn('price', array(
            'header'    => __('Price'),
            'align'     => 'center',
            'type'      => 'currency',
            'index'     => 'price'
        ));
        $this->addColumn('position', array(
            'header'    => __('Position'),
            'width'     => '70px',
            'align'     => 'center',
            'type'      => 'number',
            'index'     => 'position',
            'editable'  => true
            //'renderer'  => 'adminhtml/widget_grid_column_renderer_input'
        ));
        
        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return Mage::getUrl('*/*/grid', array('_current'=>true));
    }
    
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('selected_proudcts');
        if (is_null($products)) {
            $products = Mage::registry('category')->getProductsPosition();
            return array_keys($products);
        }
        /*else {
            $products = explode(',', $products);
        }*/
        return $products;
    }
}
