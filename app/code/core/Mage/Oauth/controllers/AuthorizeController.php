<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * oAuth authorize controller
 *
 * @category    Mage
 * @package     Mage_Oauth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Oauth_AuthorizeController extends Mage_Core_Controller_Front_Action
{
    /**
     * Session name
     *
     * @var string
     */
    protected $_sessionName = 'customer/session';

    /**
     * Init authorize page
     *
     * @param bool $simple      Is simple page?
     * @return Mage_Oauth_AuthorizeController
     */
    protected function _initForm($simple = false)
    {
        /** @var $server Mage_Oauth_Model_Server */
        $server = Mage::getModel('Mage_Oauth_Model_Server');
        /** @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton($this->_sessionName);

        $isException = false;
        try {
            $server->checkAuthorizeRequest();
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Mage_Oauth_Exception $e) {
            $isException = true;
            $session->addException($e, $this->__('An error occurred. Your authorization request is invalid.'));
        } catch (Exception $e) {
            $isException = true;
            $session->addException($e, $this->__('An error occurred.'));
        }

        $this->loadLayout();
        $layout = $this->getLayout();
        $logged = $session->isLoggedIn();

        $contentBlock = $layout->getBlock('content');
        if ($logged) {
            $contentBlock->unsetChild('oauth.authorize.form');
            /** @var $block Mage_Oauth_Block_Authorize_Button */
            $block = $contentBlock->getChildBlock('oauth.authorize.button');
        } else {
            $contentBlock->unsetChild('oauth.authorize.button');
            /** @var $block Mage_Oauth_Block_Authorize */
            $block = $contentBlock->getChildBlock('oauth.authorize.form');
        }

        /** @var $helper Mage_Core_Helper_Url */
        $helper = Mage::helper('Mage_Core_Helper_Url');
        $session->setAfterAuthUrl(Mage::getUrl('customer/account/login', array('_nosid' => true)))
                ->setBeforeAuthUrl($helper->getCurrentUrl());

        $block->setIsSimple($simple)
            ->setToken($this->getRequest()->getQuery('oauth_token'))
            ->setHasException($isException);
        return $this;
    }

    /**
     * Init confirm page
     *
     * @param bool $simple      Is simple page?
     * @return Mage_Oauth_AuthorizeController
     */
    protected function _initConfirmPage($simple = false)
    {
        $this->loadLayout();

        /** @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton($this->_sessionName);
        try {
            /** @var $server Mage_Oauth_Model_Server */
            $server = Mage::getModel('Mage_Oauth_Model_Server');

            /** @var $block Mage_Oauth_Block_Authorize */
            $block = $this->getLayout()->getBlock('oauth.authorize.confirm');
            $block->setIsSimple($simple);

            /** @var $token Mage_Oauth_Model_Token */
            $token = $server->authorizeToken($session->getCustomerId(), Mage_Oauth_Model_Token::USER_TYPE_CUSTOMER);

            /** @var $helper Mage_Oauth_Helper_Data */
            $helper = Mage::helper('Mage_Oauth_Helper_Data');

            if (($callback = $helper->getFullCallbackUrl($token))) { //false in case of OOB
                $this->_redirectUrl($callback . ($simple ? '&simple=1' : ''));
                return $this;
            } else {
                $block->setVerifier($token->getVerifier());
                $session->addSuccess($this->__('Authorization confirmed.'));
            }
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Mage_Oauth_Exception $e) {
            $session->addException($e, $this->__('An error occurred. Your authorization request is invalid.'));
        } catch (Exception $e) {
            $session->addException($e, $this->__('An error occurred on confirm authorize.'));
        }

        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();

        return $this;
    }

    /**
     * Init reject page
     *
     * @param bool $simple      Is simple page?
     * @return Mage_Oauth_AuthorizeController
     */
    protected function _initRejectPage($simple = false)
    {
        $this->loadLayout();

        /** @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton($this->_sessionName);
        try {
            /** @var $server Mage_Oauth_Model_Server */
            $server = Mage::getModel('Mage_Oauth_Model_Server');

            /** @var $block Mage_Oauth_Block_Authorize */
            $block = $this->getLayout()->getBlock('oauth.authorize.reject');
            $block->setIsSimple($simple);

            /** @var $token Mage_Oauth_Model_Token */
            $token = $server->checkAuthorizeRequest();
            /** @var $helper Mage_Oauth_Helper_Data */
            $helper = Mage::helper('Mage_Oauth_Helper_Data');

            if (($callback = $helper->getFullCallbackUrl($token, true))) {
                $this->_redirectUrl($callback . ($simple ? '&simple=1' : ''));
                return $this;
            } else {
                $session->addNotice($this->__('The application access request is rejected.'));
            }
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, $this->__('An error occurred on reject authorize.'));
        }

        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();

        return $this;
    }

    /**
     * Index action.
     *
     * @return void
     */
    public function indexAction()
    {
        $this->_initForm();
        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();
    }

    /**
     * OAuth authorize or allow decline access simple page
     *
     * @return void
     */
    public function simpleAction()
    {
        $this->_initForm(true);
        $this->_initLayoutMessages($this->_sessionName);
        $this->renderLayout();
    }

    /**
     * Confirm token authorization action
     */
    public function confirmAction()
    {
        $this->_initConfirmPage();
    }

    /**
     * Confirm token authorization simple page
     */
    public function confirmSimpleAction()
    {
        $this->_initConfirmPage(true);
    }

    /**
     * Reject token authorization action
     */
    public function rejectAction()
    {
        $this->_initRejectPage();
    }

    /**
     * Reject token authorization simple page
     */
    public function rejectSimpleAction()
    {
        $this->_initRejectPage(true);
    }
}