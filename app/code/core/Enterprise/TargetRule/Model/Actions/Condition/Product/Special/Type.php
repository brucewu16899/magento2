<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_TargetRule
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


class Enterprise_TargetRule_Model_Actions_Condition_Product_Special_Type
    extends Enterprise_TargetRule_Model_Actions_Condition_Product_Special
{

    /**
     * Set rule type
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setType('enterprise_targetrule/actions_condition_product_special_type');
    }

    /**
     * Set values for options
     *
     * @return Enterprise_TargetRule_Model_Actions_Condition_Product_Special_Type
     */
    public function loadValueOptions()
    {
        $types = Mage_Catalog_Model_Product_Type::getOptions();
        $values = array('same' => Mage::helper('enterprise_targetrule')->__('same as matched product'));
        if ($types) {
            foreach ($types as $val) {
                $values[$val['value']] = $val['value'];
            }
        }
        $this->setValueSelectOptions($values);
        return $this;
    }

    /**
     * Set type of value
     *
     * @return string
     */
    public function getValueElementType()
    {
        return 'select';
    }

    /**
     * Set rule operators
     *
     * @return Enterprise_TargetRule_Model_Actions_Condition_Product_Special_Type
     */
    public function loadOperatorOptions()
    {
        parent::loadOperatorOptions();
        $this->setOperatorOption(array(
            '=='  => Mage::helper('rule')->__('is'),
            '!='  => Mage::helper('rule')->__('is not')
        ));
        return $this;
    }

    /**
     * Retrieve rule as HTML formated string
     *
     * @return string
     */
    public function asHtml()
    {
        return $this->getTypeElementHtml()
            . Mage::helper('enterprise_targetrule')->__('Product Type %s %s',
                $this->getOperatorElementHtml(), $this->getValueElementHtml())
            . $this->getRemoveLinkHtml();
    }
}

