<?php
/**
 * Admin tax tabs block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Tax_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();

        $this->addTab('tax_rule', array(
            'label'     => __('Tax rules'),
            'title'     => __('Tax rules title'),
            'url'      => Mage::getUrl('adminhtml/tax_rule')
        ));

        $this->addTab('tax_rate', array(
            'label'     => __('Tax rates'),
            'title'     => __('Tax rates title'),
            'url'      => Mage::getUrl('adminhtml/tax_rate')
        ));

        $this->addTab('tax_class_customer', array(
            'label'     => __('Customer tax classes'),
            'title'     => __('Customer tax classes title'),
            'url'      => Mage::getUrl('adminhtml/tax_class_customer')
        ));

        $this->addTab('tax_class_product', array(
            'label'     => __('Product tax classes'),
            'title'     => __('Product tax classes title'),
            'url'      => Mage::getUrl('adminhtml/tax_class_product')
        ));
    }
}