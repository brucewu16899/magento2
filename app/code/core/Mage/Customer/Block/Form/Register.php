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
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Customer register form block
 *
 * @category   Mage
 * @package    Mage_Customer
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_Block_Form_Register extends Mage_Directory_Block_Data
{
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle(__('Create New Customer Account'));
        return parent::_prepareLayout();
    }
    
    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return Mage::getUrl('customer/account/createPost', array('_secure'=>true));
    }
    
    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        $url = $this->getData('back_url');
        if (is_null($url)) {
            $url = Mage::getUrl('customer/account/login');
        }
        return $url;
    }
    
    /**
     * Retrieve form data
     *
     * @return Varien_Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $data = new Varien_Object(Mage::getSingleton('customer/session')->getCustomerFormData(true));
            $this->setData('form_data', $data);
        }
        return $data;
    }
    
    public function getCountryId()
    {
        if ($countryId = $this->getFormData()->getCountryId()) {
            return $countryId;
        }
        return parent::getCountryId();
    }

    public function getRegion()
    {
        if ($region = $this->getFormData()->getRegion()) {
            return $region;
        }
        elseif ($region = $this->getFormData()->getRegionId()) {
            return $region;
        }
        return null;
    }
    
    
}
