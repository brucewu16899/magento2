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

class Mage_Adminhtml_Block_Notification_Window extends Mage_Adminhtml_Block_Notification_Toolbar
{
    protected $_httpsObjectUrl = 'http://widgets.magentocommerce.com/messagePopupWindow';
    protected $_httpObjectUrl = 'http://widgets.magentocommerce.com/messagePopupWindow';

    protected function _construct()
    {
        parent::_construct();

        $this->setHeaderText(addslashes($this->__('Incoming Message')));
        $this->setCloseText(addslashes($this->__('close')));
        $this->setReadDetailsText(addslashes($this->__('Read details')));
        $this->setMajorText(addslashes($this->__('MAJOR')));
        $this->setMinorText(addslashes($this->__('MINOR')));
        $this->setCriticalText(addslashes($this->__('CRITICAL')));


        $this->setNoticeText($this->getLastNotice()->getTitle());
        $this->setNoticeUrl($this->getLastNotice()->getUrl());

        $severity = 'SEVERITY_MINOR';
        switch ($this->getLastNotice()->getSeverity()) {
            default:
            case 3:
                $severity = 'SEVERITY_MINOR';
                break;
            case 2:
                $severity = 'SEVERITY_MAJOR';
                break;
            case 1:
                $severity = 'SEVERITY_CRITICAL';
                break;
        }

        $this->setNoticeSeverity($severity);
    }

    /**
     * Can we show notification window
     *
     * @return bool
     */
    public function canShow()
    {
        $firstVisit = Mage::getSingleton('admin/session')->getData('is_first_visit', true);
        if (!$firstVisit) {
            return false;
        }

        return $this->isShow();
    }


    /**
     * Return swf object url
     *
     * @return string
     */
    public function getObjectUrl()
    {
        if (!empty($_SERVER['HTTPS'])) {
            return $this->_httpsObjectUrl;
        } else {
            return $this->_httpObjectUrl;
        }
    }

    public function getLastNotice()
    {
        return Mage::helper('adminnotification')->getLatestNotice();
    }

}