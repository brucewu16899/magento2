<?php
/**
 * Category filter
 *
 * @package    Mage
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Category_Filter extends Varien_Object 
{
    public function __construct($data=array()) 
    {
        parent::__construct($data);
    }
    
    public function getId()
    {
        return $this->getFilterId();
    }
    
    public function getResource()
    {
        return Mage::getResourceSingleton('catalog/category_filter');
    }
    
    public function load($filterId)
    {
        
    }

    public function getLabel()
    {
        return $this->getAttribute()->getCode();
    }
    
    public function getAttribute()
    {
        $data = array(
            'attribute_id'  => $this->getAttributeId(),
            'attribute_code'=> $this->getAttributeCode(),
            'data_type'     => $this->getDataType(),
            'multiple'      => $this->getMultiple(),
            /*'data_input'    => $this->getAttributeId(),
            'data_saver',
            'data_source',
            'delitable',
            'validation',
            'input_format',
            'output_format',
            'required',
            'searchable',
            'comparale',*/
        );
        return Mage::getModel('catalog/product_attribute')->setData($data);
    }
    
    public function getValue()
    {
        $current    = $this->getCurrentValues();
        if (!empty($current)) {
            return  empty($current[$this->getId()]) ? array() : $current[$this->getId()];
        }
        else {
            return array();
        }
    }
    
    public function getLinks()
    {
        $arr = array();
        /**
         * array(
         *      ['attribute_value']
         *      ['product_count']
         * )
         */
        $availableValues  = $this->getAvailableValues();
        $available = array();
        $availableCount = array();
        
        if (is_array($availableValues)) {
            foreach ($availableValues as $availableInfo) {
            	$available[] = $availableInfo['attribute_value'];
            	$availableCount[$availableInfo['attribute_value']] = $availableInfo['product_count'];
            }
        }
        
        $current    = $this->getCurrentValues();
        $currentValues = $this->getValue();
        
        $request = clone Mage::registry('controller')->getRequest();

        $values = Mage::getModel('catalog/product_attribute')
            ->setAttributeId($this->getAttributeId())
            ->getOptions();
            
        foreach ($values as $value) {
            if (null !== $availableValues) {
                $arrParam = array('filter' => $current);
                if (in_array($value->getId(), $available) && !in_array($value->getId(), $currentValues)) {
                    $arrParam['filter'][$this->getId()][] = $value->getId();
                    $arr[] = array(
                        'label' => $value->getValue(),
                        'count' => $availableCount[$value->getId()],
                        'url'   => Mage::getUrl('*/*/*', $request->setParam('array',$arrParam)->getParams()),
                    );
                }
                elseif (in_array($value->getId(), $currentValues)) {
                    unset($arrParam['filter'][$this->getId()]);
                    $arr[] = array(
                        'label' => 'Clear (' . $value->getValue() . ')',
                        'url'   => Mage::getUrl('*/*/*', $request->setParam('array',$arrParam)->getParams()),
                    );
                }
            }
            else {
                $arr[] = array( 
                    'label' => $value->getValue(),
                    'url'   => Mage::getUrl(),
                );
            }
        }
        return $arr;
    }
    
}