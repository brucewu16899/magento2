<?php
/**
 * Adminhtml tax rates controller
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Tax_RateController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->loadLayout('baseframe');
        $this->_setActiveMenu('catalog');
        $this->_addBreadcrumb(__('catalog'), __('catalog title'), Mage::getUrl('adminhtml/catalog'));
        $this->_addBreadcrumb(__('tax'), __('tax title'));

        $this->_addContent($block = $this->getLayout()->createBlock('adminhtml/tax_rate', 'tax'));

        $this->renderLayout();
    }
}