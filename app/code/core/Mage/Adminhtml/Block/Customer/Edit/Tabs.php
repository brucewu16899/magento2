<?php
/**
 * admin customer left menu
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Customer_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('customer_info_tabs');
        $this->setDestElementId('customer_edit_form');
        $this->setTitle(__('Customer Information'));
    }

    protected function _beforeToHtml()
    {
        if (Mage::registry('customer')->getId()) {
            $this->addTab('view', array(
                'label'     => __('Customer view'),
                'content'   => $this->getLayout()->createBlock('adminhtml/customer_edit_tab_view')->toHtml(),
                'active'    => true
            ));
        }

        $this->addTab('account', array(
            'label'     => __('Account information'),
            'content'   => $this->getLayout()->createBlock('adminhtml/customer_edit_tab_account')->initForm()->toHtml(),
            'active'    => Mage::registry('customer')->getId() ? false : true
        ));

        $this->addTab('addresses', array(
            'label'     => __('Addresses'),
            'content'   => $this->getLayout()->createBlock('adminhtml/customer_edit_tab_addresses')->initForm()->toHtml(),
        ));

        if (Mage::registry('customer')->getId()) {
            $this->addTab('orders', array(
                'label'     => __('Orders'),
                'content'   => $this->getLayout()->createBlock('adminhtml/customer_edit_tab_orders')->toHtml(),
            ));
    
            $this->addTab('cart', array(
                'label'     => __('Shopping cart'),
                'content'   => '<h3>Shopping cart</h3>'#$this->getLayout()->createBlock('adminhtml/customer_edit_tab_cart')->toHtml(),
            ));
    
            $this->addTab('wishlist', array(
                'label'     => __('Wishlist'),
                'content'   => '<h3>Wishlist</h3>'#$this->getLayout()->createBlock('adminhtml/customer_edit_tab_wishlist')->toHtml(),
            ));
    
            $this->addTab('newsletter', array(
                'label'     => __('Newsletter'),
                'content'   => '<h3>Newsletter</h3>',
            ));
    
            $this->addTab('tags', array(
                'label'     => __('Product tags'),
                'content'   => '<h3>Product tags</h3>'#$this->getLayout()->createBlock('adminhtml/customer_edit_tab_tags')->toHtml(),
            ));
    
            $this->addTab('reviews', array(
                'label'     => __('Product reviews'),
                'content'   => '<h3>Product reviews</h3>'#$this->getLayout()->createBlock('adminhtml/customer_edit_tab_reviews')->toHtml(),
            ));
        }
        Varien_Profiler::stop('customer/tabs');
        return parent::_beforeToHtml();
    }
}
