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
 * @package     Mage_Connect
 * @subpackage  Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Extension controller
 *
 * @category    Mage
 * @package     Mage_Connect
 * @subpackage  Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Connect_Adminhtml_Extension_CustomController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Redirect to edit Extension Package action
     *
     */
    public function indexAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Magentu Connect'))
             ->_title($this->__('Package Extensions'));

        Mage::app()->getStore()->setStoreId(1);
        $this->_forward('edit');
    }

    /**
     * Edit Extension Package Form
     *
     */
    public function editAction()
    {
        $this->_title($this->__('System'))
             ->_title($this->__('Magentu Connect'))
             ->_title($this->__('Package Extensions'))
             ->_title($this->__('Edit Extension'));

        $this->loadLayout();
        $this->_setActiveMenu('system/extension/custom');
        $this->renderLayout();
    }

    /**
     * Reset Extension Package form data
     *
     */
    public function resetAction()
    {
        Mage::getSingleton('connect/session')->unsCustomExtensionPackageFormData();
        $this->_redirect('*/*/edit');
    }

    /**
     * Load Local Extension Package
     *
     */
    public function loadAction()
    {
        $packageName = $this->getRequest()->getParam('id');
        if ($packageName) {
            $session = Mage::getSingleton('connect/session');
            try {
                $data = Mage::helper('connect')->loadLocalPackage($packageName);
                if (!$data) {
                    Mage::throwException(Mage::helper('connect')->__("Failed to load package data"));
                }
                $data = array_merge($data, array('file_name' => $packageName));
                $session->setCustomExtensionPackageFormData($data);
                $session->addSuccess(Mage::helper('connect')->__("Package %s data was successfully loaded", $packageName));
            } catch (Exception $e) {
                $session->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/edit');
    }

    /**
     * Save Extension Package
     *
     */
    public function saveAction()
    {
        $session = Mage::getSingleton('connect/session');
        $p = $this->getRequest()->getPost();

        if (!empty($p['_create'])) {
            $create = true;
            unset($p['_create']);
        }

        if ($p['file_name'] == '') {
            $p['file_name'] = $p['name'];
        }

        $session->setCustomExtensionPackageFormData($p);
        try {
            $ext = Mage::getModel('connect/extension');
            $ext->setData($p);
            if ($ext->savePackage()) {
                $session->addSuccess(Mage::helper('connect')->__('Package data was successfully saved'));
            } else {
                $session->addError(Mage::helper('connect')->__('There was a problem saving package data'));
                $this->_redirect('*/*/edit');
            }
            if (empty($create)) {
                $this->_redirect('*/*/edit');
            } else {
                Mage::app()->getStore()->setStoreId(1);
                $this->_forward('create');
            }
        } catch (Mage_Core_Exception $e){
            $session->addError($e->getMessage());
            $this->_redirect('*/*');
        } catch (Exception $e){
            $session->addException($e, Mage::helper('connect')->__("Failed to save package"));
            $this->_redirect('*/*');
        }
    }

    /**
     * Create new Extension Package
     *
     */
    public function createAction()
    {
        $session = Mage::getSingleton('connect/session');
        try {
            $p = $this->getRequest()->getPost();
            $session->setCustomExtensionPackageFormData($p);
            $ext = Mage::getModel('connect/extension');
            $ext->setData($p);
            $ext->createPackage();
            $this->_redirect('*/*');
        } catch(Mage_Core_Exception $e){
            $session->addError($e->getMessage());
            $this->_redirect('*/*');
        } catch(Exception $e){
            $session->addException($e, Mage::helper('connect')->__("Failed to create package"));
            $this->_redirect('*/*');
        }
    }

    /**
     * Load Grid with Local Packages
     *
     */
    public function loadtabAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Grid for loading packages
     *
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
