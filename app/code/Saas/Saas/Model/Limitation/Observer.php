<?php
/**
 * Module limitation observer
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Saas_Model_Limitation_Observer
{
    /**
     * @var Mage_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * @var Saas_Saas_Model_Limitation_SpecificationInterface
     */
    protected $_specification;

    /**
     * @var Saas_Saas_Helper_Data
     */
    protected $_saasHelper;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Saas_Saas_Model_Limitation_Specification_Chain $specification
     * @param Saas_Saas_Helper_Data $saasHelper
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Saas_Saas_Model_Limitation_Specification_Chain $specification,
        Saas_Saas_Helper_Data $saasHelper
    ) {
        $this->_request = $request;
        $this->_specification = $specification;
        $this->_saasHelper = $saasHelper;
    }

    /**
     * Limit module functionality
     *
     * @param Varien_Event_Observer $observer
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function limitFunctionality(Varien_Event_Observer $observer)
    {
        if (!$this->_specification->isAllowed($this->_request)) {
            $this->_saasHelper->customizeNoRoutForward($this->_request);
        }
    }
}
