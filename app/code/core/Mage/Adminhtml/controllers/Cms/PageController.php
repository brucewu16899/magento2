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
 * Cms manage pages controller
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 * @author      Alexander Stadnitski <alexander@varien.com>
 * @author      Michael Bessolov <michael@varien.com>
 */

class Mage_Adminhtml_Cms_PageController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('cms/page')
            ->_addBreadcrumb(__('CMS'), __('CMS'))
            ->_addBreadcrumb(__('Manage Pages'), __('Manage Pages'))
        ;
        return $this;
    }

    public function indexAction()
    {
        $this->_initAction()
            ->_addContent($this->getLayout()->createBlock('adminhtml/cms_page'))
            ->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('page_id');
        $model = Mage::getModel('cms/page');

        if ($id) {
            $model->load($id);
            if (! $model->getId()) {
                Mage::getSingleton('adminhtml/session')->addError(__('This page no longer exists'));
                $this->_redirect('*/*/');
                return;
            }
        }

        // set entered data if was error when we do save
        $data = Mage::getSingleton('adminhtml/session')->getPageData(true);
        if (! empty($data)) {
            $model->setData($data);
        }

        Mage::register('cms_page', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? __('Edit Page') : __('New Page'), $id ? __('Edit Page') : __('New Page'))
            ->_addContent($this->getLayout()->createBlock('adminhtml/cms_page_edit')->setData('action', Mage::getUrl('*/cms_page/save')))
            ->_addLeft($this->getLayout()->createBlock('adminhtml/cms_page_edit_tabs'))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            $model = Mage::getModel('cms/page');
//            if ($id = $this->getRequest()->getParam('page_id')) {
//                $model->load($id);
//                if ($id != $model->getId()) {
//                    Mage::getSingleton('adminhtml/session')->addError(__('The page you are trying to save no longer exists'));
//                    Mage::getSingleton('adminhtml/session')->setPageData($data);
//                    $this->_redirect('*/*/edit', array('page_id' => $this->getRequest()->getParam('page_id')));
//                    return;
//                }
//            }
            $model->setData($data);
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Page was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setPageData(false);
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setPageData($data);
                $this->_redirect('*/*/edit', array('page_id' => $this->getRequest()->getParam('page_id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('page_id')) {
            try {
                $model = Mage::getModel('cms/page');
                $model->setId($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(__('Page was successfully deleted'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('page_id' => $this->getRequest()->getParam('page_id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(__('Unable to find a page to delete'));
        $this->_redirect('*/*/');
    }

    protected function _isAllowed()
    {
	    return Mage::getSingleton('admin/session')->isAllowed('cms/page');
    }

}
