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
 * System config form block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_System_Config_Form extends Mage_Backend_Block_Widget_Form
{

    const SCOPE_DEFAULT = 'default';
    const SCOPE_WEBSITES = 'websites';
    const SCOPE_STORES   = 'stores';

    /**
     * Config data array
     *
     * @var array
     */
    protected $_configData;

    /**
     * Adminhtml config data instance
     *
     * @var Mage_Adminhtml_Model_Config_Data
     */
    protected $_configDataObject;

    /**
     * Enter description here...
     *
     * @var Varien_Simplexml_Element
     */
    protected $_configRoot;

    /**
     * System configuration
     *
     * @var Mage_Backend_Model_System_ConfigInterface
     */
    protected $_systemConfig;

    /**
     * Enter description here...
     *
     * @var Mage_Adminhtml_Block_System_Config_Form_Fieldset
     */
    protected $_defaultFieldsetRenderer;

    /**
     * Enter description here...
     *
     * @var Mage_Adminhtml_Block_System_Config_Form_Field
     */
    protected $_defaultFieldRenderer;

    /**
     * List of fieldset
     *
     * @var array
     */
    protected $_fieldsets = array();

    /**
     * Translated scope labels
     *
     * @var array
     */
    protected $_scopeLabels = array();

    /**
     * @var Mage_Backend_Helper_Data
     */
    protected $_helper;


    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_helper = isset($data['helper']) ? isset($data['helper']) : null;
        $this->_systemConfig = isset($data['systemConfig']) ?
            $data['systemConfig'] :
            Mage::getSingleton('Mage_Backend_Model_System_Config');

        parent::__construct($data);

        $this->_scopeLabels = array(
            self::SCOPE_DEFAULT  => $this->_getHelper()->__('[GLOBAL]'),
            self::SCOPE_WEBSITES => $this->_getHelper()->__('[WEBSITE]'),
            self::SCOPE_STORES   => $this->_getHelper()->__('[STORE VIEW]'),
        );
    }

    /**
     * Get helper object
     *
     * @return Mage_Backend_Helper_Data
     */
    protected function _getHelper()
    {
        if (null === $this->_helper) {
            $this->_helper = $this->_getHelperRegistry()->get('Mage_Backend_Helper_Data');
        }
        return $this->_helper;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Backend_Block_System_Config_Form
     */
    protected function _initObjects()
    {
        $this->_configRoot = Mage::getConfig()->getNode(null, $this->getScope(), $this->getScopeCode());

        $this->_configDataObject = $this->_objectFactory->getModelInstance('Mage_Backend_Model_System_Config_Data')
            ->setSection($this->getSectionCode())
            ->setWebsite($this->getWebsiteCode())
            ->setStore($this->getStoreCode());

        $this->_configData = $this->_configDataObject->load();

        $this->_defaultFieldsetRenderer = Mage::getBlockSingleton('Mage_Backend_Block_System_Config_Form_Fieldset');
        $this->_defaultFieldRenderer = Mage::getBlockSingleton('Mage_Backend_Block_System_Config_Form_Field');
        return $this;
    }

    /**
     * Initialize form
     *
     * @return Mage_Backend_Block_System_Config_Form
     */
    public function initForm()
    {
        $this->_initObjects();

        /** @var Varien_Data_Form $form */
        $form = $this->_getObjectFactory('Varien_Data_Form');

        $sections = $this->_systemConfig->getSections();
        if (empty($sections)) {
            $sections = array();
        }

        foreach ($sections as $section) {
            /* @var array $section */
            if (false == $this->_canShowField($section)) {
                continue;
            }

            foreach ($section['groups'] as $groups) {
                /** @var array $groups */
                usort($groups, array($this, '_sortForm'));
                foreach ($groups as $group){
                    /* @var $group array */
                    if (false == $this->_canShowField($group)) {
                        continue;
                    }
                    $this->_initGroup($group, $section, $form);
                }
            }
        }
        $this->setForm($form);
        return $this;
    }

    /**
     * Initialize element group
     *
     * @param array $group
     * @param array $section
     * @param Varien_Data_Form $form
     */
    protected function _initGroup($group, $section, $form)
    {
        if (isset($group['frontend_model'])) {
            $fieldsetRenderer = Mage::getBlockSingleton((string)$group['frontend_model']);
        } else {
            $fieldsetRenderer = $this->_defaultFieldsetRenderer;
        }

        $fieldsetRenderer->setForm($this);
        $fieldsetRenderer->setConfigData($this->_configData);
        $fieldsetRenderer->setGroup($group);

        if ($this->_systemConfig->hasChildren($group, $this->getWebsiteCode(), $this->getStoreCode())) {

            $helperName = $this->_systemConfig->getAttributeModule($section, $group);

            $fieldsetConfig = array('legend' => $this->_getHelperRegistry()->get($helperName)->__($group['label']));
            if (isset($group['comment'])) {
                $fieldsetConfig['comment'] = $this->_getHelperRegistry()->get($helperName)->__($group['comment']);
            }
            if (array_key_exists($group, 'expanded')) {
                $fieldsetConfig['expanded'] = (bool)$group['expanded'];
            }

            $fieldset = $form->addFieldset($section['id'] . '_' . $group['id'], $fieldsetConfig)
                ->setRenderer($fieldsetRenderer);
            $this->_prepareFieldOriginalData($fieldset, $group);
            $this->_addElementTypes($fieldset);

            if (isset($group['clone_fields'])) {
                if (isset($group['clone_model'])) {
                    $cloneModel = $this->_objectFactory->getModelInstance($group['clone_model']);
                } else {
                    Mage::throwException(
                        'Config form fieldset clone model required to be able to clone fields'
                    );
                }
                foreach ($cloneModel->getPrefixes() as $prefix) {
                    $this->initFields($fieldset, $group, $section, $prefix['field'], $prefix['label']);
                }
            } else {
                $this->initFields($fieldset, $group, $section);
            }

            $this->_fieldsets[$group['id']] = $fieldset;
        }
    }

    /**
     * Return dependency block object
     *
     * @return Mage_Backend_Block_Widget_Form_Element_Dependence
     */
    protected function _getDependence()
    {
        if (!$this->getChildBlock('element_dependence')){
            $this->addChild('element_dependence', 'Mage_Backend_Block_Widget_Form_Element_Dependence');
        }
        return $this->getChildBlock('element_dependence');
    }

    /**
     * Init fieldset fields
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param array $group
     * @param array $section
     * @param string $fieldPrefix
     * @param string $labelPrefix
     * @return Mage_Backend_Block_System_Config_Form
     */
    public function initFields($fieldset, $group, $section, $fieldPrefix='', $labelPrefix='')
    {
        if (!$this->_configDataObject) {
            $this->_initObjects();
        }

        // Extends for config data
        $configDataAdditionalGroups = array();

        $fields = isset($group['fields']) ? $group['fields'] : array();
        foreach ($fields as $elements) {
            /** @var array $elements  */

            // sort either by sort_order or by child node values bypassing the sort_order
            $elements = $this->_sortElements($group, $fieldset, $elements);

            foreach ($elements as $element) {
                if (false == $this->_canShowField($element)) {
                    continue;
                }

                /**
                 * Look for custom defined field path
                 */
                $path = (isset($element['config_path'])) ? $element['config_path'] : '';

                if (empty($path)) {
                    $path = $section['id'] . '/' . $group['id'] . '/' . $fieldPrefix . $element['id'];
                } elseif (strrpos($path, '/') > 0) {
                    // Extend config data with new section group
                    $groupPath = substr($path, 0, strrpos($path, '/'));
                    if (false == isset($configDataAdditionalGroups[$groupPath])) {
                        $this->_configData = $this->_configDataObject->extendConfig(
                            $groupPath,
                            false,
                            $this->_configData
                        );
                        $configDataAdditionalGroups[$groupPath] = true;
                    }
                }

                $this->_initElement($element, $fieldset, $group, $section, $path, $fieldPrefix, $labelPrefix);
            }
        }
        return $this;
    }

    /**
     * @param array $group
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param array $elements
     * @return mixed
     */
    protected function _sortElements($group, $fieldset, $elements)
    {
        if (isset($group['sort_fields']) && isset($group['sort_fields']['by'])) {
            $fieldset->setSortElementsByAttribute($group['sort_fields']['by'],
                isset($group['sort_fields']['direction_desc']) ? SORT_DESC : SORT_ASC
            );
        } else {
            usort($elements, array($this, '_sortForm'));
        }
        return $elements;
    }

    /**
     * Initialize form element
     *
     * @param array $element
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param array $group
     * @param array $section
     * @param string $path
     * @param string $fieldPrefix
     * @param string $labelPrefix
     */
    protected function _initElement($element, $fieldset, $group, $section, $path, $fieldPrefix = '', $labelPrefix = '')
    {
        $elementId = $section['id'] . '_' . $group['id'] . '_' . $fieldPrefix . $element->getName();

        if (array_key_exists($path, $this->_configData)) {
            $data = $this->_configData[$path];
            $inherit = false;
        } else {
            $data = $this->_configRoot->descend($path);
            $inherit = true;
        }
        $fieldRenderer = $this->_getFieldRenderer($element);

        $fieldRenderer->setForm($this);
        $fieldRenderer->setConfigData($this->_configData);

        $helperName = $this->_systemConfig->getAttributeModule($section, $group, $element);
        $fieldType = isset($element['type']) ? $element['type'] : 'text';
        $name = 'groups[' . $group['id'] . '][fields][' . $fieldPrefix . $element['id'] . '][value]';
        $label = $this->_getHelperRegistry()->get($helperName)->__($labelPrefix)
            . ' ' . $this->_getHelperRegistry()->get($helperName)->__((string)$element['label']);
        $hint = isset($element['hint']) ? $this->_getHelperRegistry()->get($helperName)->__($element['hint']) : '';

        if (isset($element['backend_model'])) {
            $data = $this->_fetchBackendModelData($element, $path, $data);
        }

        $comment = $this->_prepareFieldComment($element, $helperName, $data);
        $tooltip = $this->_prepareFieldTooltip($element, $helperName);

        if (isset($element['depends'])) {
            $this->_processElementDependencies($element, $section, $group, $elementId, $fieldPrefix);
        }

        $field = $fieldset->addField($elementId, $fieldType, array(
            'name' => $name,
            'label' => $label,
            'comment' => $comment,
            'tooltip' => $tooltip,
            'hint' => $hint,
            'value' => $data,
            'inherit' => $inherit,
            'class' => isset($element['frontend_class']) ? $element['frontend_class'] : '',
            'field_config' => $element,
            'scope' => $this->getScope(),
            'scope_id' => $this->getScopeId(),
            'scope_label' => $this->getScopeLabel($element),
            'can_use_default_value' => $this->canUseDefaultValue(
                isset($element['show_in_default']) ? (int)$element['show_in_default'] : 0
            ),
            'can_use_website_value' => $this->canUseWebsiteValue(
                isset($element['show_in_website']) ? (int)$element['show_in_website'] : 0
            ),
        ));
        $this->_applyFieldConfiguration($field, $element);

        $field->setRenderer($fieldRenderer);

        if (isset($element['source_model'])) {
            $field->setValues($this->_extractDataFromSourceModel($element, $path, $fieldType));
        }
    }

    /**
     * Retrieve field renderer block
     *
     * @param array $element
     * @return Mage_Adminhtml_Block_System_Config_Form_Field
     */
    protected function _getFieldRenderer($element)
    {
        if (isset($element['frontend_model'])) {
            $fieldRenderer = Mage::getBlockSingleton($element['frontend_model']);
            return $fieldRenderer;
        } else {
            $fieldRenderer = $this->_defaultFieldRenderer;
            return $fieldRenderer;
        }
    }

    /**
     * Retrieve data from backend model
     *
     * @param array $element
     * @param string $path
     * @param mixed $data
     * @return mixed
     */
    protected function _fetchBackendModelData($element, $path, $data)
    {
        $model = Mage::getModel($element['backend_model']);
        if (!$model instanceof Mage_Core_Model_Config_Data) {
            Mage::throwException('Invalid config field backend model: ' . $element['backend_model']);
        }
        $model->setPath($path)
            ->setValue($data)
            ->setWebsite($this->getWebsiteCode())
            ->setStore($this->getStoreCode())
            ->afterLoad();
        $data = $model->getValue();
        return $data;
    }

    /**
     * Apply element dependencies from configuration
     *
     * @param array $element
     * @param array $section
     * @param array $group
     * @param string $elementId
     * @param string $fieldPrefix
     */
    protected function _processElementDependencies($element, $section, $group, $elementId, $fieldPrefix = '')
    {
        foreach ($element['depends'] as $dependent) {
            /* @var array $dependent */
            $dependentId = $section['id'] . '_' . $group['id'] . '_' . $fieldPrefix . $dependent['id'];
            $shouldBeAddedDependence = true;
            $dependentValue = $dependent['value'];
            if (isset($dependent['separator'])) {
                $dependentValue = explode($dependent['separator'], $dependentValue);
            }
            $dependentFieldName = $fieldPrefix . $dependent['id'];
            $dependentField = $group['fields'][$dependentFieldName];
            /*
            * If dependent field can't be shown in current scope and real dependent config value
            * is not equal to preferred one, then hide dependence fields by adding dependence
            * based on not shown field (not rendered field)
            */
            if (!$this->_canShowField($dependentField)) {
                $dependentFullPath = $section['id'] . '/' . $group['id'] . '/' . $fieldPrefix . $dependent['id'];
                $dependentValueInStore = Mage::getStoreConfig($dependentFullPath, $this->getStoreCode());
                if (is_array($dependentValue)) {
                    $shouldBeAddedDependence = !in_array($dependentValueInStore, $dependentValue);
                } else {
                    $shouldBeAddedDependence = $dependentValue != $dependentValueInStore;
                }
            }
            if ($shouldBeAddedDependence) {
                $this->_getDependence()
                    ->addFieldMap($elementId, $elementId)
                    ->addFieldMap($dependentId, $dependentId)
                    ->addFieldDependence($elementId, $dependentId, $dependentValue);
            }
        }
    }

    /**
     * Apply custom element configuration
     *
     * @param Varien_Data_Form_Element_Abstract $field
     * @param array $element
     */
    protected function _applyFieldConfiguration($field, $element)
    {
        $this->_prepareFieldOriginalData($field, $element);

        if (isset($element['validate'])) {
            $field->addClass($element['validate']);
        }

        if ('multiselect' === $element['type'] && isset($element['can_be_empty'])) {
            $field->setCanBeEmpty(true);
        }
    }

    /**
     * Retrieve source model option list
     *
     * @param array $element
     * @param string $path
     * @param string $fieldType
     * @return array
     */
    protected function _extractDataFromSourceModel($element, $path, $fieldType)
    {
        $factoryName = $element['source_model'];
        $method = false;
        if (preg_match('/^([^:]+?)::([^:]+?)$/', $factoryName, $matches)) {
            array_shift($matches);
            list($factoryName, $method) = array_values($matches);
        }

        $sourceModel = Mage::getSingleton($factoryName);
        if ($sourceModel instanceof Varien_Object) {
            $sourceModel->setPath($path);
        }
        if ($method) {
            if ($fieldType == 'multiselect') {
                $optionArray = $sourceModel->$method();
            } else {
                $optionArray = array();
                foreach ($sourceModel->$method() as $key => $value) {
                    if (is_array($value)) {
                        $optionArray[] = $value;
                    } else {
                        $optionArray[] = array('label' => $value, 'value' => $key);
                    }
                }
            }
        } else {
            $optionArray = $sourceModel->toOptionArray($fieldType == 'multiselect');
        }
        return $optionArray;
    }

    /**
     * Return config root node for current scope
     *
     * @return Varien_Simplexml_Element
     */
    public function getConfigRoot()
    {
        if (empty($this->_configRoot)) {
            $this->_configRoot = Mage::getConfig()->getNode(null, $this->getScope(), $this->getScopeCode());
        }
        return $this->_configRoot;
    }

    /**
     * Set "original_data" array to the element, composed from array elements with scalar values
     *
     * @param Varien_Data_Form_Element_Abstract $field
     * @param array $data
     */
    protected function _prepareFieldOriginalData($field, $data)
    {
        $originalData = array();
        foreach ($data as $key => $value) {
            if (!is_array($value)) {
                $originalData[$key] = $value;
            }
        }
        $field->setOriginalData($originalData);
    }

    /**
     * Support models "getCommentText" method for field note generation
     *
     * @param array $element
     * @param string $helper
     * @param string $currentValue
     * @return string
     */
    protected function _prepareFieldComment($element, $helper, $currentValue)
    {
        $comment = '';
        if (isset($element['comment'])) {
            if (is_array($element['comment'])) {
                if (isset($element['comment']['model'])) {
                    $model = Mage::getModel($element['comment']['model']);
                    if (method_exists($model, 'getCommentText')) {
                        $comment = $model->getCommentText($element, $currentValue);
                    }
                }
            } else {
                $comment = $this->_getHelperRegistry()->get($helper)->__($element['comment']);
            }
        }
        return $comment;
    }

    /**
     * Prepare additional comment for field like tooltip
     *
     * @param array $element
     * @param string $helper
     * @return string
     */
    protected function _prepareFieldTooltip($element, $helper)
    {
        if (isset($element['tooltip'])) {
            return $this->_getHelperRegistry()->get($helper)->__($element['tooltip']);
        } elseif (isset($element['tooltip_block'])) {
            return $this->getLayout()->createBlock($element['tooltip_block'])->toHtml();
        }
        return '';
    }

    /**
     * Append dependence block at then end of form block
     *
     * @param string $html
     * @return string
     */
    protected function _afterToHtml($html)
    {
        if ($this->_getDependence()) {
            $html .= $this->_getDependence()->toHtml();
        }
        $html = parent::_afterToHtml($html);
        return $html;
    }

    /**
     * Sort elements
     *
     * @param array $a
     * @param array $b
     * @return boolean
     */
    protected function _sortForm($a, $b)
    {
        $aSortOrder = isset($a['sortOrder']) ? (int)$a['sortOrder'] : 0;
        $bSortOrder = isset($b['sortOrder']) ? (int)$b['sortOrder'] : 0;
        return $aSortOrder < $bSortOrder ? -1 : ($aSortOrder > $bSortOrder ? 1 : 0);
    }

    /**
     * Check if can use default value
     *
     * @param int $fieldValue
     * @return boolean
     */
    public function canUseDefaultValue($fieldValue)
    {
        if ($this->getScope() == self::SCOPE_STORES && $fieldValue) {
            return true;
        }
        if ($this->getScope() == self::SCOPE_WEBSITES && $fieldValue) {
            return true;
        }
        return false;
    }

    /**
     * Check if can use website value
     *
     * @param int $fieldValue
     * @return boolean
     */
    public function canUseWebsiteValue($fieldValue)
    {
        if ($this->getScope() == self::SCOPE_STORES && $fieldValue) {
            return true;
        }
        return false;
    }

    /**
     * Checking field visibility
     *
     * @param   array $field
     * @return  bool
     */
    protected function _canShowField($field)
    {
        $ifModuleEnabled = isset($field['if_module_enabled']) ?  trim($field['if_module_enabled']) : false;

        if ($ifModuleEnabled &&
            false == $this->_getHelperRegistry()->get('Mage_Core_Helper_Data')->isModuleEnabled($ifModuleEnabled)) {
            return false;
        }
        $showInDefault = array_key_exists($field, 'show_in_default') ? (bool)$field['show_in_default'] : false;
        $showInWebsite = array_key_exists($field, 'show_in_website') ? (bool)$field['show_in_website'] : false;
        $showInStore = array_key_exists($field, 'show_in_store') ? (bool)$field['show_in_store'] : false;
        $hideIfSingleStore = array_key_exists($field, 'hide_in_single_store_mode') ?
            (int)$field['hide_in_single_store_mode'] : 0;

        $fieldIsDisplayable = $showInDefault || $showInWebsite || $showInStore;

        if (Mage::app()->isSingleStoreMode() && $fieldIsDisplayable) {
            return !$hideIfSingleStore;
        }

        $result = true;
        switch ($this->getScope()) {
            case self::SCOPE_DEFAULT:
                $result = (int)$showInDefault;
                break;
            case self::SCOPE_WEBSITES:
                $result = (int)$showInWebsite;
                break;
            case self::SCOPE_STORES:
                $result = (int)$showInStore;
                break;
        }
        return $result;
    }

    /**
     * Retrieve current scope
     *
     * @return string
     */
    public function getScope()
    {
        $scope = $this->getData('scope');
        if (is_null($scope)) {
            if ($this->getStoreCode()) {
                $scope = self::SCOPE_STORES;
            } elseif ($this->getWebsiteCode()) {
                $scope = self::SCOPE_WEBSITES;
            } else {
                $scope = self::SCOPE_DEFAULT;
            }
            $this->setScope($scope);
        }

        return $scope;
    }

    /**
     * Retrieve label for scope
     *
     * @param array $element
     * @return string
     */
    public function getScopeLabel($element)
    {
        $showInStore = array_key_exists($element, 'show_in_store') ? (int)$element['show_in_store'] : 0;
        $showInWebsite = array_key_exists($element, 'show_in_website') ? (int)$element['show_in_website'] : 0;

        if ($showInStore == 1) {
            return $this->_scopeLabels[self::SCOPE_STORES];
        } elseif ($showInWebsite == 1) {
            return $this->_scopeLabels[self::SCOPE_WEBSITES];
        }
        return $this->_scopeLabels[self::SCOPE_DEFAULT];
    }

    /**
     * Get current scope code
     *
     * @return string
     */
    public function getScopeCode()
    {
        $scopeCode = $this->getData('scope_code');
        if (is_null($scopeCode)) {
            if ($this->getStoreCode()) {
                $scopeCode = $this->getStoreCode();
            } elseif ($this->getWebsiteCode()) {
                $scopeCode = $this->getWebsiteCode();
            } else {
                $scopeCode = '';
            }
            $this->setScopeCode($scopeCode);
        }

        return $scopeCode;
    }

    /**
     * Get current scope code
     *
     * @return int|string
     */
    public function getScopeId()
    {
        $scopeId = $this->getData('scope_id');
        if (is_null($scopeId)) {
            if ($this->getStoreCode()) {
                $scopeId = Mage::app()->getStore($this->getStoreCode())->getId();
            } elseif ($this->getWebsiteCode()) {
                $scopeId = Mage::app()->getWebsite($this->getWebsiteCode())->getId();
            } else {
                $scopeId = '';
            }
            $this->setScopeId($scopeId);
        }
        return $scopeId;
    }

    /**
     * Get additional element types
     *
     * @return array
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'export' => Mage::getConfig()
                ->getBlockClassName('Mage_Backend_Block_System_Config_Form_Field_Export'),
            'import' => Mage::getConfig()
                 ->getBlockClassName('Mage_Backend_Block_System_Config_Form_Field_Import'),
            'allowspecific' => Mage::getConfig()
                ->getBlockClassName('Mage_Backend_Block_System_Config_Form_Field_Select_Allowspecific'),
            'image' => Mage::getConfig()
                ->getBlockClassName('Mage_Backend_Block_System_Config_Form_Field_Image'),
            'file' => Mage::getConfig()
                ->getBlockClassName('Mage_Backend_Block_System_Config_Form_Field_File')
        );
    }

    /**
     * Temporary moved those $this->getRequest()->getParam('blabla') from the code accross this block
     * to getBlala() methods to be later set from controller with setters
     */
    /**
     * Enter description here...
     *
     * @TODO delete this methods when {^see above^} is done
     * @return string
     */
    public function getSectionCode()
    {
        return $this->getRequest()->getParam('section', '');
    }

    /**
     * Enter description here...
     *
     * @TODO delete this methods when {^see above^} is done
     * @return string
     */
    public function getWebsiteCode()
    {
        return $this->getRequest()->getParam('website', '');
    }

    /**
     * Enter description here...
     *
     * @TODO delete this methods when {^see above^} is done
     * @return string
     */
    public function getStoreCode()
    {
        return $this->getRequest()->getParam('store', '');
    }
}
