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
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Button widget
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Widget_Button extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getType()
    {
        return ($type=$this->getData('type')) ? $type : 'button';
    }

    public function getOnClick()
    {
        if (!$this->getData('on_click')) {
            return $this->getData('onclick');
        }
        return $this->getData('on_click');
    }

    public function toHtml()
    {
        $html = '<button id="'.$this->getId().'" name="'.$this->getName().'" type="'.$this->getType().'" class="scalable '.$this->getClass().'" onclick="'.$this->getOnClick().'" style="'.$this->getStyle().'" value="'.$this->getValue().'">';
        $html.= '<table cellspacing="0"><tr><td class="tl"></td><td class="tr"></td></tr>';
        $html.= '<tr><td class="ml"><span>'.$this->getLabel().'</span></td><td class="mr"></td></tr>';
        $html.= '<tr><td class="bl"></td><td class="br"></td></tr></table></button>';

        return $html;
    }
}
