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
 * @package    Mage_CatalogRule
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_CatalogRule_Model_Rule_Condition_Product extends Mage_Rule_Model_Condition_Abstract
{
    public function getAttributeObject()
    {
        $obj = Mage::getSingleton('eav/config')
            ->getAttribute('catalog_product', $this->getAttribute());
        if (!$obj->getEntity()) {
            $obj->setEntity(Mage::getResourceSingleton('catalog/product'));
        }
        return $obj;
    }

    public function loadAttributeOptions()
    {
        $productAttributes = Mage::getResourceSingleton('catalog/product')
            ->loadAllAttributes()->getAttributesByCode();

        $attributes = array();
        foreach ($productAttributes as $attr) {
            if (!$attr->getIsVisible()) {
                continue;
            }
            $attributes[$attr->getAttributeCode()] = $attr->getFrontend()->getLabel();
        }

        $attributes['attribute_set_id'] = Mage::helper('catalogrule')->__('Attribute Set');
        $attributes['categories'] = Mage::helper('catalogrule')->__('Category');

        asort($attributes);
        $this->setAttributeOption($attributes);

        return $this;
    }

    public function getValueOption($option=null)
    {
        if (!$this->getData('value_option')) {
            if ($this->getAttribute()==='attribute_set_id') {
                $options = Mage::getResourceModel('eav/entity_attribute_set_collection')
                    ->load()->toOptionHash();
                $this->setData('value_option', $options);
            } elseif ($this->getAttributeObject()->usesSource()) {
                $optionsArr = $this->getAttributeObject()->getSource()->getAllOptions();
                $options = array();
                foreach ($optionsArr as $o) {
                    if (is_array($o['value'])) {

                    } else {
                        $options[$o['value']] = $o['label'];
                    }
                }
                $this->setData('value_option', $options);
            }
        }
        return $this->getData('value_option'.(!is_null($option) ? '/'.$option : ''));
    }

    public function getValueSelectOptions()
    {
        if (!$this->getData('value_select_options')) {
            if ($this->getAttribute()==='attribute_set_id') {
                $options = Mage::getResourceModel('eav/entity_attribute_set_collection')
                    ->load()->toOptionArray();
                $this->setData('value_select_options', $options);
            } elseif ($this->getAttributeObject()->usesSource()) {
                $optionsArr = $this->getAttributeObject()->getSource()->getAllOptions();
                $this->setData('value_select_options', $optionsArr);
            }
        }
        return $this->getData('value_select_options');
    }

    public function getValueAfterElementHtml()
    {
        $html = '';

        switch ($this->getAttribute()) {
            case 'sku': case 'categories':
                $image = Mage::getDesign()->getSkinUrl('images/rule_chooser_trigger.gif');
                break;
        }

        if (!empty($image)) {
            $html = '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="' . $image . '" alt="" align="absmiddle" class="rule-chooser-trigger" title="' . Mage::helper('rule')->__('Open Chooser') . '" /></a>';
        }
        return $html;
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        return $element;
    }

    public function collectValidatedAttributes($productCollection)
    {
        switch ($this->getAttribute()) {
            case 'categories':
                break;

            default:
                $productCollection->addAttributeToSelect($this->getAttribute());
                break;
        }
        return $this;
    }

    public function getInputType()
    {
        if ($this->getAttribute()==='attribute_set_id') {
            return 'select';
        }
        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'select':
                return 'select';

            case 'date':
                return 'date';

            default:
                return 'string';
        }
    }

    public function getValueElementType()
    {
        if ($this->getAttribute()==='attribute_set_id') {
            return 'select';
        }
        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'select':
                return 'select';

            case 'date':
                return 'date';

            default:
                return 'text';
        }
    }

    public function getValueElement()
    {
        $element = parent::getValueElement();

        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'date':
                $element->setImage(Mage::getDesign()->getSkinUrl('images/grid-cal.gif'));
                break;
        }

        return $element;
    }

    public function getValueElementChooserUrl()
    {
        $url = false;
        switch ($this->getAttribute()) {
            case 'sku': case 'categories':
                $url = 'adminhtml/promo_catalog/chooser/attribute/'.$this->getAttribute();
                break;
        }
        return $url!==false ? Mage::helper('adminhtml')->getUrl($url) : '';
    }

    public function getExplicitApply()
    {
        switch ($this->getAttribute()) {
            case 'sku': case 'categories':
                return true;
        }
        switch ($this->getAttributeObject()->getFrontendInput()) {
            case 'date':
                return true;
        }
        return false;
    }
}
