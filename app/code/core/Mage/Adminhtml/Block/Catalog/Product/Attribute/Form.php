<?php
/**
 * Product attribute add/edit form
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Attribute_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/attribute/save.phtml');
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Attribute Properties')));

        $fieldset->addField('attribute_name', 'text',
                            array(
                                'name' => 'attribute_name',
                                'label' => __('Attribute Name'),
                                'title' => __('Attribute Name Title'),
                                'class' => 'required-entry',
                                'required' => true,
                            )
        );

        $fieldset->addField('attribute_code', 'text',
                            array(
                                'name' => 'attribute_code',
                                'label' => __('Attribute Code'),
                                'title' => __('Attribute Code Title'),
                                'class' => 'required-entry',
                                'required' => true,
                            )
        );

        $fieldset->addField('default_value', 'text',
                            array(
                                'name' => 'default_value',
                                'label' => __('Default Value'),
                                'title' => __('Default Value Title'),
                            )
        );

        $fieldset->addField('attribute_model', 'text',
                            array(
                                'name' => 'attribute_model',
                                'label' => __('Attribute Model'),
                                'title' => __('Attribute Model Title'),
                            )
        );

        $fieldset->addField('backend_model', 'text',
                            array(
                                'name' => 'backend_model',
                                'label' => __('Backend Model'),
                                'title' => __('Backend Model Title'),
                            )
        );

        $fieldset->addField('backend_type', 'text',
                            array(
                                'name' => 'backend_type',
                                'label' => __('Backend Type'),
                                'title' => __('Backend Type Title'),
                            )
        );

        $fieldset->addField('backend_table', 'text',
                            array(
                                'name' => 'backend_table',
                                'label' => __('Backend Table'),
                                'title' => __('Backend Table Title'),
                            )
        );

        $fieldset->addField('frontend_model', 'text',
                            array(
                                'name' => 'frontend_model',
                                'label' => __('Frontend Model'),
                                'title' => __('Frontend Model Title'),
                            )
        );

        $fieldset->addField('frontend_input', 'text',
                            array(
                                'name' => 'frontend_input',
                                'label' => __('Frontend Input'),
                                'title' => __('Frontend Input Title'),
                            )
        );

        $fieldset->addField('frontend_label', 'text',
                            array(
                                'name' => 'frontend_label',
                                'label' => __('Frontend Label'),
                                'title' => __('Frontend Label Title'),
                            )
        );

        $fieldset->addField('frontend_class', 'text',
                            array(
                                'name' => 'frontend_class',
                                'label' => __('Frontend Class'),
                                'title' => __('Frontend Class Title'),
                            )
        );

        $fieldset->addField('source_model', 'text',
                            array(
                                'name' => 'source_model',
                                'label' => __('Source Model'),
                                'title' => __('Source Model Title'),
                            )
        );

        $fieldset->addField('is_global', 'radios',
                            array(
                                'name' => 'is_global',
                                'label' => __('Global'),
                                'title' => __('Global Title'),
                                'values' => $this->_addYesNoOptions()
                            )
        );

        $fieldset->addField('is_visible', 'radios',
                            array(
                                'name' => 'is_visible',
                                'label' => __('Visible'),
                                'title' => __('Visible Title'),
                                'values' => $this->_addYesNoOptions()
                            )
        );

        $fieldset->addField('is_required', 'radios',
                            array(
                                'name' => 'is_required',
                                'label' => __('Required'),
                                'title' => __('Required Title'),
                                'values' => $this->_addYesNoOptions()
                            )
        );

        $fieldset->addField('is_searchable', 'radios',
                            array(
                                'name' => 'is_searchable',
                                'label' => __('Searchable'),
                                'title' => __('Searchable Title'),
                                'values' => $this->_addYesNoOptions()
                            )
        );

        $fieldset->addField('is_filterable', 'radios',
                            array(
                                'name' => 'is_filterable',
                                'label' => __('Filterable'),
                                'title' => __('Filterable Title'),
                                'values' => $this->_addYesNoOptions()
                            )
        );

        $fieldset->addField('is_comparable', 'radios',
                            array(
                                'name' => 'is_comparable',
                                'label' => __('Comparable'),
                                'title' => __('Comparable Title'),
                                'values' => $this->_addYesNoOptions()
                            )
        );

        $attributeId = $this->getRequest()->getParam('attributeId', false);

        if( $attributeId ) {
            $fieldset->addField('attribute_id', 'hidden',
                                array(
                                    'name' => 'attribute_id',
                                    'no-span' => true,
                                )
            );

            $form->getElement('attribute_code')->setDisabled('true');
            $form->getElement('attribute_code')->setRequired(false);
            $form->getElement('attribute_code')->setClass(null);

            $attributeObject = Mage::getSingleton('eav/entity_attribute')->load($attributeId);
            $form->setValues($attributeObject->getData());

            $this->setAttributeId($attributeId);
            $this->setAttributeData($attributeObject);

            $this->assign('header', __('Edit Attribute'));
        } else {
            $this->assign('header', __('Add new Attribute'));
            $this->setAttributeData(new Varien_Object());
        }

        $form->setAction(Mage::getUrl('*/*/save'));
        $form->setUseContainer(true);
        $form->setId('attribute_form');
        $form->setMethod('POST');
        $this->setForm($form);
        return parent::_prepareForm();
    }

    protected function _addYesNoOptions()
    {
        return array(
            array(
                'value' => 0,
                'label' => 'No',
                'title' => 'Yes Title'
            ),
            array(
                'value' => 1,
                'label' => 'Yes',
                'title' => 'No Title'
            )
        );
    }

    protected function _initChildren()
    {
        $this->setChild('toolbar',
            $this->getLayout()->createBlock('adminhtml/catalog_product_attribute_toolbar_save')
        );

        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Back'),
                    'onclick'   => 'window.location.href=\''.Mage::getUrl('*/*/').'\''
                ))
        );

        $this->setChild('save_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Save Attribute'),
                    'onclick'   => 'attribute_form.submit()'
                ))
        );

        $this->setChild('delete_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => __('Delete Attribute'),
                    'onclick'   => 'window.location.href=\''.Mage::getUrl('*/*/delete/attribute/'. $this->getAttributeId() .'').'\''
                ))
        );
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getDeleteButtonHtml()
    {
        if( $this->getAttributeData()->getIsUserDefined() == 0 ) {
            return;
        }
        return $this->getChildHtml('delete_button');
    }

    protected function _getHeader()
    {
        return __('Edit Attribute');
    }

}