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
 * Product attribute add/edit form main tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Front extends Mage_Backend_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $attributeObject = Mage::registry('entity_attribute');

        $form = new Varien_Data_Form(array('id' => 'edit_form', 'action' => $this->getData('action'), 'method' => 'post'));

        $yesnoSource = Mage::getModel('Mage_Backend_Model_Config_Source_Yesno')->toOptionArray();;


        $fieldset = $form->addFieldset(
            'front_fieldset',
            array(
                'legend'=>Mage::helper('Mage_Catalog_Helper_Data')->__('Frontend Properties')
            )
        );

        $fieldset->addField('is_searchable', 'select', array(
            'name'     => 'is_searchable',
            'label'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Use in Quick Search'),
            'title'    => Mage::helper('Mage_Catalog_Helper_Data')->__('Use in Quick Search'),
            'values'   => $yesnoSource,
        ));

        $fieldset->addField('is_visible_in_advanced_search', 'select', array(
            'name' => 'is_visible_in_advanced_search',
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Use in Advanced Search'),
            'title' => Mage::helper('Mage_Catalog_Helper_Data')->__('Use in Advanced Search'),
            'values' => $yesnoSource,
        ));

        $fieldset->addField('is_comparable', 'select', array(
            'name' => 'is_comparable',
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Comparable on Front - end'),
            'title' => Mage::helper('Mage_Catalog_Helper_Data')->__('Comparable on Front - end'),
            'values' => $yesnoSource,
        ));

        $fieldset->addField('is_filterable', 'select', array(
            'name' => 'is_filterable',
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__("Use In Layered Navigation"),
            'title' => Mage::helper('Mage_Catalog_Helper_Data')->__('Can be used only with catalog input type Dropdown, Multiple Select
        and Price'),
            'note' => Mage::helper('Mage_Catalog_Helper_Data')->__('Can be used only with catalog input type Dropdown, Multiple Select
        and Price'),
            'values' => array(
                array('value' => '0', 'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('No')),
                array('value' => '1', 'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Filterable(with results)')),
                array('value' => '2', 'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Filterable(no results)')),
            ),
        ));

        $fieldset->addField('is_filterable_in_search', 'select', array(
            'name' => 'is_filterable_in_search',
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__("Use In Search Results Layered Navigation"),
            'title' => Mage::helper('Mage_Catalog_Helper_Data')->__('Can be used only with catalog input type Dropdown, Multiple Select
        and Price'),
            'note' => Mage::helper('Mage_Catalog_Helper_Data')->__('Can be used only with catalog input type Dropdown, Multiple Select
        and Price'),
            'values' => $yesnoSource,
        ));

        $fieldset->addField('is_used_for_promo_rules', 'select', array(
            'name' => 'is_used_for_promo_rules',
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Use for Promo Rule Conditions'),
            'title' => Mage::helper('Mage_Catalog_Helper_Data')->__('Use for Promo Rule Conditions'),
            'values' => $yesnoSource,
        ));

        $fieldset->addField('position', 'text', array(
            'name' => 'position',
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Position'),
            'title' => Mage::helper('Mage_Catalog_Helper_Data')->__('Position in Layered Navigation'),
            'note' => Mage::helper('Mage_Catalog_Helper_Data')->__('Position of attribute in layered navigation block'),
            'class' => 'validate - digits',
        ));

        $fieldset->addField('is_wysiwyg_enabled', 'select', array(
            'name' => 'is_wysiwyg_enabled',
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Enable WYSIWYG'),
            'title' => Mage::helper('Mage_Catalog_Helper_Data')->__('Enable WYSIWYG'),
            'values' => $yesnoSource,
        ));

        $htmlAllowed = $fieldset->addField('is_html_allowed_on_front', 'select', array(
            'name' => 'is_html_allowed_on_front',
            'label' => Mage::helper('Mage_Catalog_Helper_Data')->__('Allow HTML Tags on Frontend'),
            'title' => Mage::helper('Mage_Catalog_Helper_Data')->__('Allow HTML Tags on Frontend'),
            'values' => $yesnoSource,
        ));
        if (!$attributeObject->getId() || $attributeObject->getIsWysiwygEnabled()) {
            $attributeObject->setIsHtmlAllowedOnFront(1);
        }

        $fieldset->addField('is_visible_on_front', 'select', array(
            'name'      => 'is_visible_on_front',
            'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Visible on Product View Page on Front - end'),
            'title'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Visible on Product View Page on Front - end'),
            'values'    => $yesnoSource,
        ));

        $fieldset->addField('used_in_product_listing', 'select', array(
            'name'      => 'used_in_product_listing',
            'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Used in Product Listing'),
            'title'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Used in Product Listing'),
            'note'      => Mage::helper('Mage_Catalog_Helper_Data')->__('Depends on design theme'),
            'values'    => $yesnoSource,
        ));
        $fieldset->addField('used_for_sort_by', 'select', array(
            'name'      => 'used_for_sort_by',
            'label'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Used for Sorting in Product Listing'),
            'title'     => Mage::helper('Mage_Catalog_Helper_Data')->__('Used for Sorting in Product Listing'),
            'note'      => Mage::helper('Mage_Catalog_Helper_Data')->__('Depends on design theme'),
            'values'    => $yesnoSource,
        ));

        // define field dependencies
        $this->setChild(
            'form_after',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Form_Element_Dependence')
                ->addFieldMap("is_wysiwyg_enabled", 'wysiwyg_enabled')
                ->addFieldMap("is_html_allowed_on_front", 'html_allowed_on_front')
                ->addFieldMap("frontend_input", 'frontend_input_type')
                ->addFieldDependence('wysiwyg_enabled', 'frontend_input_type', 'textarea')
                ->addFieldDependence('html_allowed_on_front', 'wysiwyg_enabled', '0')
        );


        $form->setValues($attributeObject->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
