<?php
/**
 * Adminhtml super product links grid
 *
 * @package    Mage
 * @subpackage Adminhtml
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 * @license    http://www.opensource.org/licenses/osl-3.0.php
 * @author	   Ivan Chepurnyi <mitch@varien.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct() 
	{
		parent::__construct();
		$this->setDefaultFilter(array('in_products'=>1));
        $this->setUseAjax(true);
		$this->setId('super_product_links');
	}
	
	protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
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
        $product =  Mage::registry('product');
       	$collection = Mage::getResourceModel('catalog/product_collection')
       		->addAttributeToSelect('name')
            ->addAttributeToSelect('sku')
            ->addAttributeToSelect('price')
            ->addFieldToFilter('attribute_set_id',$product->getAttributeSetId());
            
       	foreach ($product->getSuperAttributesIds() as $attributeId) {
       		$collection->addAttributeToSelect($attributeId);
       	}

        $this->setCollection($collection);
        
        return parent::_prepareCollection();
    }
	
    protected function _getSelectedProducts()
    {
        $products = $this->getRequest()->getPost('products', null);
                        
        if (!is_array($products)) {
            $products = null;
        }
        
        return $products;
    }
    
    protected function _prepareColumns()
    {
    	$product = Mage::registry('product');
    	$attributes = $product->getSuperAttributes(true);
    	
    	
        $this->addColumn('in_products', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_products',
            'values'    => $this->_getSelectedProducts(),
            'align'     => 'center',
            'index'     => 'entity_id',
            'renderer'	=> 'adminhtml/catalog_product_edit_tab_super_config_grid_renderer_checkbox',
            'attributes' => $attributes
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
                        
        
        foreach ($attributes as $attribute) {
		    $this->addColumn($attribute->getAttributeCode(), array(
		        'header'    => __($attribute->getFrontend()->getLabel()),
		        'index'     => $attribute->getAttributeCode(),
		        'type'		=> $attribute->getSourceModel() ? 'options' : 'number',
		        'options'   => $attribute->getSourceModel() ? $this->getOptions($attribute) : ''
		    ));
        }
         
        
        return parent::_prepareColumns();
    }
    
    public function getOptions($attribute) {
    	$result = array();
    	foreach ($attribute->getSource()->getAllOptions() as $option) {
    		if($option['value']!='') {
     			$result[$option['value']] = $option['label'];
    		}    		
    	}
    	
    	return $result;
    }
    
    public function getGridUrl()
    {
        return Mage::getUrl('*/*/superConfig', array('_current'=>true));
    }
}// Class Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Super_Config_Grid END