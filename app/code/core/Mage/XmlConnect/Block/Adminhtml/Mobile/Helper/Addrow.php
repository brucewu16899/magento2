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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_XmlConnect_Block_Adminhtml_Mobile_Helper_Addrow extends Varien_Data_Form_Element_Button
{
    public function getElementHtml()
    {
        $html = $this->getBeforeElementHtml() . '<button id="'.$this->getHtmlId().'" name="'.$this->getName()
            . '" value="'.$this->getEscapedValue().'"'
            . $this->serialize($this->getHtmlAttributes())
            . '" <span>'.$this->getEscapedValue().'</span></button>'.$this->getAfterElementHtml();
        return $html;
    }

    public function getBeforeElementHtml()
    {
        return $this->getData('before_element_html');
    }

    public function toHtml()
    {
        $js = new Mage_Core_Block_Template;
        $js->setTemplate('xmlconnect/addrow.phtml');
        $js->setOptions($this->getOptions());
        return parent::toHtml() . $js->toHtml();
    }
}
