<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Promotions_Catalogrule extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Core_Model_StoreManagerInterface $storeManager
     * @param Mage_Core_Model_Url $urlModel
     * @param Mage_CatalogRule_Model_Resource_Rule_Collection $ruleCollection
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Core_Model_StoreManagerInterface $storeManager,
        Mage_Core_Model_Url $urlModel,
        Mage_CatalogRule_Model_Resource_Rule_Collection $ruleCollection,
        array $data = array()
    ) {
        parent::__construct($context, $storeManager, $urlModel, $data);
        $this->setCollection($ruleCollection);
    }

    /**
     * Initialize grid, set defaults
     *
     */
    public function _construct()
    {
        parent::_construct();
        $this->setId('related_catalogrule_grid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
        $this->setVarNameFilter('related_catalogrule_filter');
        if ($this->_getBanner() && $this->_getBanner()->getId()) {
            $this->setDefaultFilter(array('in_banner_catalogrule'=>1));
        }
    }

    /**
     * Set custom filter for in banner catalog flag
     *
     * @param string $column
     * @return Enterprise_Banner_Block_Adminhtml_Banner_Edit_Tab_Promotions_Salesrule
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_banner_catalogrule') {
            $ruleIds = $this->_getSelectedRules();
            if (empty($ruleIds)) {
                $ruleIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('rule_id', array('in'=>$ruleIds));
            } else {
                if ($ruleIds) {
                    $this->getCollection()->addFieldToFilter('rule_id', array('nin'=>$ruleIds));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Create grid columns
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('in_banner_catalogrule', array(
            'header_css_class' => 'a-center',
            'type'      => 'checkbox',
            'name'      => 'in_banner_catalogrule',
            'values'    => $this->_getSelectedRules(),
            'align'     => 'center',
            'index'     => 'rule_id'
        ));
        $this->addColumn('catalogrule_rule_id', array(
            'header'    => Mage::helper('Mage_CatalogRule_Helper_Data')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'rule_id',
        ));

        $this->addColumn('catalogrule_name', array(
            'header'    => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Rule'),
            'align'     =>'left',
            'index'     => 'name',
        ));

        $this->addColumn('catalogrule_from_date', array(
            'header'    => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Start on'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'index'     => 'from_date',
        ));

        $this->addColumn('catalogrule_to_date', array(
            'header'    => Mage::helper('Mage_CatalogRule_Helper_Data')->__('End on'),
            'align'     => 'left',
            'width'     => '120px',
            'type'      => 'date',
            'default'   => '--',
            'index'     => 'to_date',
        ));

        $this->addColumn('catalogrule_is_active', array(
            'header'    => Mage::helper('Mage_CatalogRule_Helper_Data')->__('Status'),
            'align'     => 'left',
            'width'     => '80px',
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                1 => 'Active',
                0 => 'Inactive',
            ),
        ));


        return parent::_prepareColumns();
    }

    /**
     * Ajax grid URL getter
     *
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/catalogRuleGrid', array('_current'=>true));
    }

    protected function _getSelectedRules()
    {
        $rules = $this->getSelectedCatalogRules();
        if (is_null($rules)) {
            $rules = $this->getRelatedCatalogRule();
        }
        return $rules;
    }

    /**
     * Get related sales rules by current banner
     *
     * @return array
     */
    public function getRelatedCatalogRule()
    {
        return $this->_getBanner()->getRelatedCatalogRule();
    }

    /**
     * Get current banner model
     *
     * @return Enterprise_Banner_Model_Banner
     */
    protected function _getBanner()
    {
        return Mage::registry('current_banner');
    }
}
