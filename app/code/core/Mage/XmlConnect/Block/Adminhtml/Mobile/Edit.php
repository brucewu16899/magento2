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

class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Class construct
     */
    public function __construct()
    {
        $this->_objectId    = 'application_id';
        $this->_controller  = 'adminhtml_mobile';
        $this->_blockGroup  = 'xmlconnect';
        parent::__construct();

        $this->_updateButton('save', 'label', $this->__('Save Application'));

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('xmlconnect')->__('Save and Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);

        if (Mage::registry('current_app')->getId()) {
            $this->_addButton('submit_application_button', array(
                'label' =>  Mage::helper('xmlconnect')->__('Submit Application'),
                'onclick'    => 'setLocation(\''. $this->getUrl('*/*/submission',
                    array('application_id' => Mage::registry('current_app')->getId())).'\')',
                'class'     => 'save'
            ));
        }

        $this->_formScripts[] = 'function saveAndContinueEdit() {
            editForm.submit($(\'edit_form\').action + \'back/edit/\');}';

        $this->_updateButton('delete', 'label', $this->__('Delete Application'));
        if (Mage::registry('current_app')->getStatus() == Mage_XmlConnect_Model_Application::APP_STATUS_SUCCESS) {
            $this->_updateButton('delete', 'disabled', 'disabled');
        }

        $this->_updateButton('back', 'label', $this->__('Back to Application Edit'));
    }

    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->addJs('jscolor/jscolor.js');
        $this->getLayout()->getBlock('head')->addJs('scriptaculous/scriptaculous.js');
        return parent::_prepareLayout();
    }

    /**
     * Get form header title
     * @return string
     */
    public function getHeaderText()
    {
        $app = Mage::registry('current_app');
        if ($app && $app->getId()) {
            return $this->__('Edit App "%s"', $this->htmlEscape($app->getName()));
        } else {
            return $this->__('New Application');
        }
    }
}
