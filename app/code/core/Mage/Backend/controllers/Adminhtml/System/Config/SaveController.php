<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * System Configuration Save Controller
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Backend_Adminhtml_System_Config_SaveController extends Mage_Backend_Controller_System_ConfigAbstract
{
    /**
     * Backend Config Model Factory
     *
     * @var Mage_Backend_Model_Config_Factory
     */
    protected $_configFactory;

    /**
     * Core Config Model
     *
     * @var Mage_Core_Model_Config
     */
    protected $_configModel;

    /**
     * Event manager model
     *
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * Application model
     *
     * @var Mage_Core_Model_App
     */
    protected $_app;

    /**
     * Constructor
     *
     * @param Zend_Controller_Request_Abstract $request
     * @param Zend_Controller_Response_Abstract $response
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Authorization $authorization
     * @param Mage_Backend_Model_Config_Structure $configStructure
     * @param Mage_Core_Model_Config $configModel
     * @param Mage_Backend_Model_Config_Factory $configFactory
     * @param Mage_Core_Model_Event_Manager $eventManager
     * @param Mage_Core_Model_App $app
     * @param Mage_Backend_Model_Auth_StorageInterface $authSession
     * @param array $invokeArgs
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(Zend_Controller_Request_Abstract $request,
        Zend_Controller_Response_Abstract $response,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Authorization $authorization,
        Mage_Backend_Model_Config_Structure $configStructure,
        Mage_Core_Model_Config $configModel,
        Mage_Backend_Model_Config_Factory $configFactory,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Core_Model_App $app,
        Mage_Backend_Model_Auth_StorageInterface $authSession,
        array $invokeArgs = array()
    ) {
        parent::__construct($request, $response, $objectManager, $frontController,
            $authorization, $configStructure, $authSession, $invokeArgs
        );

        $this->_authorization = $authorization;
        $this->_configStructure = $configStructure;
        $this->_configFactory = $configFactory;
        $this->_eventManager = $eventManager;
        $this->_app = $app;
        $this->_configModel = $configModel;
    }

    /**
     * Save configuration
     */
    public function indexAction()
    {
        try {
            if (false == $this->_isSectionAllowed($this->getRequest()->getParam('section'))) {
                throw new Exception($this->_getHelper()->__('This section is not allowed.'));
            }

            // custom save logic
            $this->_saveSection();
            $section = $this->getRequest()->getParam('section');
            $website = $this->getRequest()->getParam('website');
            $store   = $this->getRequest()->getParam('store');

            $configData = array(
                'section' => $section,
                'website' => $website,
                'store' => $store,
                'groups' => $this->_getGroupsForSave()
            );
            /** @var Mage_Backend_Model_Config $configModel  */
            $configModel = $this->_configFactory->create(array('data' => $configData));
            $configModel->save();

            // re-init configuration
            $this->_configModel->reinit();

            $this->_eventManager->dispatch('admin_system_config_section_save_after', array(
                'website' => $website, 'store' => $store, 'section' => $section
            ));

            $this->_app->reinitStores();

            // website and store codes can be used in event implementation, so set them as well
            $this->_eventManager->dispatch("admin_system_config_changed_section_{$section}", array(
                'website' => $website, 'store' => $store
            ));
            $this->_session->addSuccess($this->_getHelper()->__('The configuration has been saved.'));
        } catch (Mage_Core_Exception $e) {
            $messages = explode("\n", $e->getMessage());
            array_walk($messages, create_function(
                '$message', '$this->_session->addError($message);'
            ));
        } catch (Exception $e) {
            $this->_session->addException($e,
                $this->_getHelper()->__('An error occurred while saving this configuration:') . ' ' . $e->getMessage());
        }

        $this->_saveState($this->getRequest()->getPost('config_state'));
        $this->_redirect('*/system_config/edit', array('_current' => array('section', 'website', 'store')));
    }

    /**
     * Get groups for save
     *
     * @return array|null
     */
    protected function _getGroupsForSave()
    {
        $groups = $this->getRequest()->getPost('groups');
        $files = $this->getRequest()->getFiles('groups');

        if (isset($files['name']) && is_array($files['name'])) {
            /**
             * Carefully merge $_FILES and $_POST information
             * None of '+=' or 'array_merge_recursive' can do this correct
             */
            foreach ($files['name'] as $groupName => $group) {
                $data = $this->_processNestedGroups($group);
                if (false == empty($data)) {
                    $groups[$groupName] = $data;
                }
            }
        }
        return $groups;
    }

    /**
     * Process nested groups
     *
     * @param mixed $group
     * @return array
     */
    protected function _processNestedGroups($group)
    {
        $data = array();

        if (isset($group['fields']) && is_array($group['fields'])) {
            foreach ($group['fields'] as $fieldName => $field) {
                if (false == empty($field['value'])) {
                    $data['fields'][$fieldName] = array('value' => $field['value']);
                }
            }
        }

        if (isset($group['groups']) && is_array($group['groups'])) {
            foreach ($group['groups'] as $groupName => $groupData) {
                $nestedGroup = $this->_processNestedGroups($groupData);
                if (false == empty($nestedGroup)) {
                    $data['groups'][$groupName] = $nestedGroup;
                }
            }
        }

        return $data;
    }

    /**
     * Custom save logic for section
     */
    protected function _saveSection()
    {
        $method = '_save' . uc_words($this->getRequest()->getParam('section'), '');
        if (method_exists($this, $method)) {
            $this->$method();
        }
    }

    /**
     * Advanced save procedure
     */
    protected function _saveAdvanced()
    {
        $this->_app->cleanCache(array('layout', Mage_Core_Model_Layout_Merge::LAYOUT_GENERAL_CACHE_TAG));
    }


}
