<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Import customer finance entity model
 *
 * @category    Enterprise
 * @package     Enterprise_ImportExport
 * @author      Magento Core Team <core@magentocommerce.com>
 * @method      array getData() getData()
 */
class Enterprise_ImportExport_Model_Import_Entity_V2_Eav_Customer_Finance
    extends Mage_ImportExport_Model_Import_Entity_V2_Eav_Customer_Abstract
{
    /**#@+
     * Permanent column names
     *
     * Names that begins with underscore is not an attribute. This name convention is for
     * to avoid interference with same attribute name.
     */
    const COLUMN_EMAIL   = 'email';
    const COLUMN_WEBSITE = '_website';
    /**#@-*/

    /**
     * Column names that holds values with particular meaning
     *
     * @var array
     */
    protected $_particularAttributes = array(
        self::COLUMN_WEBSITE,
        self::COLUMN_EMAIL,
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->_initAttributes();
    }

    /**
     * Initialize entity attributes
     *
     * @return Mage_ImportExport_Model_Import_Entity_V2_Eav_Abstract
     */
    protected function _initAttributes()
    {
        $collection = $this->_getAttributeCollection();
        /** @var $attribute Mage_Eav_Model_Attribute */
        foreach ($collection as $attribute) {
            $this->_attributes[$attribute->getAttributeCode()] = array(
                'id'          => $attribute->getId(),
                'code'        => $attribute->getAttributeCode(),
                'is_required' => $attribute->getIsRequired(),
                'type'        => $attribute->getBackendType(),
            );
        }
        return $this;
    }

    /**
     * Import data rows
     *
     * @return boolean
     */
    protected function _importData()
    {
        /** @var $importExportHelper Enterprise_ImportExport_Helper_Data */
        $importExportHelper = Mage::helper('Enterprise_ImportExport_Helper_Data');
        if (!$importExportHelper->isRewardPointsEnabled() && !$importExportHelper->isCustomerBalanceEnabled()) {
            return false;
        }

        /** @var $customer Mage_Customer_Model_Customer */
        $customer = Mage::getModel('Mage_Customer_Model_Customer');
        $rewardPointsKey =
            Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COLUMN_REWARD_POINTS;
        $customerBalanceKey =
            Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection::COLUMN_CUSTOMER_BALANCE;

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            foreach ($bunch as $rowNumber => $rowData) {
                // check row data
                if (!$this->validateRow($rowData, $rowNumber)) {
                    continue;
                }
                // load customer object
                $customerId = $this->_getCustomerId(
                    $rowData[self::COLUMN_EMAIL],
                    $rowData[self::COLUMN_WEBSITE]
                );
                if ($customer->getId() != $customerId) {
                    $customer->load($customerId);
                }
                // save finance data for customer
                foreach ($this->_attributes as $attributeCode => $attributeParams) {
                    if (isset($rowData[$attributeCode]) && strlen($rowData[$attributeCode])) {
                        $websiteId = $this->_websiteCodeToId[$rowData[self::COLUMN_WEBSITE]];
                        if ($attributeCode == $rewardPointsKey) {
                            $this->_updateRewardPoints($customer, $websiteId, $rowData[$attributeCode]);
                        } elseif ($attributeCode == $customerBalanceKey) {
                            $this->_updateCustomerBalance($customer, $websiteId, $rowData[$attributeCode]);
                        }
                    }
                }
            }
        }

        return true;
    }

    /**
     * Update reward points value for customer
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param int $websiteId
     * @param int $value reward points value
     * @return Enterprise_Reward_Model_Reward
     */
    protected function _updateRewardPoints(Mage_Customer_Model_Customer $customer, $websiteId, $value)
    {
        /** @var $rewardModel Enterprise_Reward_Model_Reward */
        $rewardModel = Mage::getModel('Enterprise_Reward_Model_Reward');
        $rewardModel->setCustomer($customer)
            ->setWebsiteId($websiteId)
            ->loadByCustomer();
        $pointsDelta = $value - $rewardModel->getPointsBalance();
        if ($pointsDelta != 0) {
            $rewardModel->setPointsDelta($pointsDelta)
                ->setAction(Enterprise_Reward_Model_Reward::REWARD_ACTION_ADMIN)
                ->setActionEntity($customer)
                ->updateRewardPoints();
        }

        return $rewardModel;
    }

    /**
     * Update store credit balance for customer
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param int $websiteId
     * @param float $value store credit balance
     * @return Enterprise_CustomerBalance_Model_Balance
     */
    protected function _updateCustomerBalance(Mage_Customer_Model_Customer $customer, $websiteId, $value)
    {
        /** @var $balanceModel Enterprise_CustomerBalance_Model_Balance */
        $balanceModel = Mage::getModel('Enterprise_CustomerBalance_Model_Balance');
        $balanceModel->setCustomer($customer)
            ->setWebsiteId($websiteId)
            ->loadByCustomer();
        $amountDelta = $value - $balanceModel->getAmount();
        if ($amountDelta != 0) {
            $balanceModel->setAmountDelta($amountDelta)
                ->setComment(Mage::helper('Enterprise_ImportExport_Helper_Data')->__('Updated during import'))
                ->save();
        }

        return $balanceModel;
    }

    /**
     * Imported entity type code getter
     *
     * @return string
     */
    public function getEntityTypeCode()
    {
        return 'customer_finance';
    }

    /**
     * Validate data row
     *
     * @param array $rowData
     * @param int $rowNumber
     * @return boolean
     */
    public function validateRow(array $rowData, $rowNumber)
    {
        if (isset($this->_validatedRows[$rowNumber])) { // check that row is already validated
            return !isset($this->_invalidRows[$rowNumber]);
        }
        $this->_validatedRows[$rowNumber] = true;
        $this->_processedEntitiesCount++;

        if (empty($rowData[self::COLUMN_WEBSITE])) {
            $this->addRowError(self::ERROR_WEBSITE_IS_EMPTY, $rowNumber, self::COLUMN_WEBSITE);
        } elseif (empty($rowData[self::COLUMN_EMAIL])) {
            $this->addRowError(self::ERROR_EMAIL_IS_EMPTY, $rowNumber, self::COLUMN_EMAIL);
        } else {
            $email   = strtolower($rowData[self::COLUMN_EMAIL]);
            $website = $rowData[self::COLUMN_WEBSITE];

            if (!Zend_Validate::is($email, 'EmailAddress')) {
                $this->addRowError(self::ERROR_INVALID_EMAIL, $rowNumber, self::COLUMN_EMAIL);
            } elseif (!isset($this->_websiteCodeToId[$website])) {
                $this->addRowError(self::ERROR_INVALID_WEBSITE, $rowNumber, self::COLUMN_WEBSITE);
            } elseif (!$this->_getCustomerId($email, $website)) {
                $this->addRowError(self::ERROR_CUSTOMER_NOT_FOUND, $rowNumber);
            } else {
                // check simple attributes
                foreach ($this->_attributes as $attributeCode => $attributeParams) {
                    if (in_array($attributeCode, $this->_ignoredAttributes)) {
                        continue;
                    }
                    if (isset($rowData[$attributeCode]) && strlen($rowData[$attributeCode])) {
                        $this->isAttributeValid($attributeCode, $attributeParams, $rowData, $rowNumber);
                    } elseif ($attributeParams['is_required']) {
                        $this->addRowError(self::ERROR_VALUE_IS_REQUIRED, $rowNumber, $attributeCode);
                    }
                }
            }
        }

        return !isset($this->_invalidRows[$rowNumber]);
    }

    /**
     * Retrieve entity attribute EAV collection
     *
     * @return Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection
     */
    protected function _getAttributeCollection()
    {
        return Mage::getResourceModel('Enterprise_ImportExport_Model_Resource_Customer_Attribute_Finance_Collection');
    }
}
