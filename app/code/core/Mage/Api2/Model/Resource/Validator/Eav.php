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
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 EAV Validator
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Api2_Model_Resource_Validator_Eav extends Mage_Api2_Model_Resource_Validator
{
    /**
     * Type of current validator
     */
    const TYPE_EAV = 'eav';

    /**
     * Form path
     *
     * @var string
     */
    protected $_formPath;

    /**
     * Entity model
     *
     * @var Mage_Core_Model_Abstract
     */
    protected $_entity;

    /**
     * Form code
     *
     * @var string
     */
    protected $_formCode;

    /**
     * Construct. Set all depends.
     *
     * Required parameteres for options:
     * - resource
     * - operation
     *
     * @param $options
     * @throws Exception If passed parameter 'resource' is wrong
     * @throws Exception If passed parameter 'operation' is empty
     * @throws Exception If config parameter 'formPath' is empty
     * @throws Exception If config parameter 'formCode' is empty
     * @throws Exception If config parameter 'entity' is wrong
     */
    public function __construct($options)
    {
        if (!isset($options['resource']) || !$options['resource'] instanceof Mage_Api2_Model_Resource) {
            throw new Exception("Passed parameter 'resource' is wrong.");
        }
        $resource = $options['resource'];
        $resourceType = $resource->getResourceType();
        $userType = $resource->getUserType();

        if (!isset($options['operation']) || empty($options['operation'])) {
            throw new Exception("Passed parameter 'operation' is empty.");
        }
        $operation = $options['operation'];

        /* @var $config Mage_Api2_Model_Config */
        $config = $resource->getConfig();

        $this->_formPath = $config->getResourceValidatorFormModel(
            $resourceType, self::TYPE_EAV, $userType);
        if (empty($this->_formPath)) {
            throw new Exception("Config parameter 'formPath' is empty.");
        }

        $this->_formCode = $config->getResourceValidatorFormCode(
            $resourceType, self::TYPE_EAV, $userType, $operation);
        if (empty($this->_formCode)) {
            throw new Exception("Config parameter 'formCode' is empty.");
        }

        $this->_entity = Mage::getModel(
            $config->getResourceValidatorEntityModel($resourceType, self::TYPE_EAV, $userType));
        if (empty($this->_entity) || !$this->_entity instanceof Mage_Core_Model_Abstract) {
            throw new Exception("Config parameter 'entity' is wrong.");
        }
    }

    /**
     * Validate entity.
     * If fails validation, then this method returns false, and
     * getErrors() will return an array of errors that explain why the
     * validation failed.
     *
     * @param  array $data
     * @return bool
     */
    public function isSatisfiedByData(array $data)
    {
        $errors = $this->_validateWithEavForm($data);
        if (true !== $errors) {
            $this->_setErrors($errors);
            return false;
        }
        return true;
    }

    /**
     * Validate entity.
     * If fails validation, then this metod return an array of errors
     * that explain why the validation failed.
     *
     * @param array $data
     * @return array|bool
     */
    protected function _validateWithEavForm($data)
    {
        /** @var $form Mage_Eav_Model_Form */
        $form = Mage::getModel($this->_formPath);
        $form->setEntity($this->_entity)
            ->setFormCode($this->_formCode)
            ->ignoreInvisible(false);

        return $form->validateData($data);
    }
}
