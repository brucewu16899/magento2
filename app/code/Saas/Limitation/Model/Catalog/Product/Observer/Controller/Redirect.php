<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Catalog_Product_Observer_Controller_Redirect
{
    /**
     * @var Mage_Backend_Model_Session
     */
    private $_session;

    /**
     * @var Saas_Limitation_Model_Limitation_Validator
     */
    private $_limitationValidator;

    /**
     * @var Saas_Limitation_Model_Limitation_LimitationInterface
     */
    private $_limitation;

    /**
     * @var string
     */
    private $_message;

    /**
     * @param Mage_Backend_Model_Session $session
     * @param Saas_Limitation_Model_Limitation_Validator $limitationValidator
     * @param Saas_Limitation_Model_Catalog_Product_Limitation $limitation
     * @param Saas_Limitation_Model_Dictionary $dictionary
     * @param string $messageCode
     */
    public function __construct(
        Mage_Backend_Model_Session $session,
        Saas_Limitation_Model_Limitation_Validator $limitationValidator,
        Saas_Limitation_Model_Catalog_Product_Limitation $limitation,
        Saas_Limitation_Model_Dictionary $dictionary,
        $messageCode
    ) {
        $this->_session = $session;
        $this->_limitationValidator = $limitationValidator;
        $this->_limitation = $limitation;
        $this->_message = $dictionary->getMessage($messageCode);
    }

    /**
     * Restrict redirect to new product creation page, if the limitation is reached
     *
     * @param Varien_Event_Observer $observer
     * @throws Mage_Catalog_Exception
     */
    public function restrictNewEntityCreation(Varien_Event_Observer $observer)
    {
        /** @var Mage_Adminhtml_Catalog_ProductController $controller */
        $controller = $observer->getEvent()->getData('controller');
        $redirectTarget = $controller->getRequest()->getParam('back');
        if ($redirectTarget == 'new' && $this->_limitationValidator->isThresholdReached($this->_limitation)) {
            throw new Mage_Catalog_Exception($this->_message);
        }
    }
}
