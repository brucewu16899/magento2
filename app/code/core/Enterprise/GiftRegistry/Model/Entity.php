<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Entity data model
 *
 * @method Enterprise_GiftRegistry_Model_Resource_Entity _getResource()
 * @method Enterprise_GiftRegistry_Model_Resource_Entity getResource()
 * @method Enterprise_GiftRegistry_Model_Entity setTypeId(int $value)
 * @method int getCustomerId()
 * @method Enterprise_GiftRegistry_Model_Entity setCustomerId(int $value)
 * @method int getWebsiteId()
 * @method Enterprise_GiftRegistry_Model_Entity setWebsiteId(int $value)
 * @method int getIsPublic()
 * @method Enterprise_GiftRegistry_Model_Entity setIsPublic(int $value)
 * @method string getUrlKey()
 * @method Enterprise_GiftRegistry_Model_Entity setUrlKey(string $value)
 * @method string getTitle()
 * @method Enterprise_GiftRegistry_Model_Entity setTitle(string $value)
 * @method string getMessage()
 * @method Enterprise_GiftRegistry_Model_Entity setMessage(string $value)
 * @method string getShippingAddress()
 * @method Enterprise_GiftRegistry_Model_Entity setShippingAddress(string $value)
 * @method string getCustomValues()
 * @method Enterprise_GiftRegistry_Model_Entity setCustomValues(string $value)
 * @method int getIsActive()
 * @method Enterprise_GiftRegistry_Model_Entity setIsActive(int $value)
 * @method string getCreatedAt()
 * @method Enterprise_GiftRegistry_Model_Entity setCreatedAt(string $value)
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_GiftRegistry_Model_Entity extends Mage_Core_Model_Abstract
{
    /**
     * XML configuration paths
     */
    const XML_PATH_OWNER_EMAIL_IDENTITY  = 'enterprise_giftregistry/owner_email/identity';
    const XML_PATH_OWNER_EMAIL_TEMPLATE  = 'enterprise_giftregistry/owner_email/template';
    const XML_PATH_SHARE_EMAIL_IDENTITY  = 'enterprise_giftregistry/sharing_email/identity';
    const XML_PATH_SHARE_EMAIL_TEMPLATE  = 'enterprise_giftregistry/sharing_email/template';
    const XML_PATH_UPDATE_EMAIL_IDENTITY = 'enterprise_giftregistry/update_email/identity';
    const XML_PATH_UPDATE_EMAIL_TEMPLATE = 'enterprise_giftregistry/update_email/template';

    /**
     * Exception code
     */
    const EXCEPTION_CODE_HAS_REQUIRED_OPTIONS = 916;

    /**
     * Type object
     * @var Enterprise_GiftRegistry_Model_Type
     */
    protected $_type = null;

    /**
     * Type id
     *
     * @var int
     */
    protected $_typeId = null;

    /**
     * Attributes array
     *
     * @var array
     */
    protected $_attributes = null;

   /**
     * Init resource model
     */
    protected function _construct() {
        $this->_init('Enterprise_GiftRegistry_Model_Resource_Entity');
        parent::_construct();
    }

    /**
     * Add items to registry
     *
     * @param array $itemsIds
     * @return int
     */
    public function addQuoteItems($itemsIds)
    {
        $skippedItems = 0;
        if (is_array($itemsIds)) {
            $quote = Mage::getModel('Mage_Sales_Model_Quote');
            $quote->setWebsite(Mage::app()->getWebsite($this->getWebsiteId()));
            $quote->loadByCustomer(Mage::getModel('Mage_Customer_Model_Customer')->load($this->getCustomerId()));

            foreach ($quote->getAllVisibleItems() as $item) {
                if (in_array($item->getId(), $itemsIds)) {
                     if (!Mage::helper('Enterprise_GiftRegistry_Helper_Data')->canAddToGiftRegistry($item)) {
                        $skippedItems++;
                        continue;
                    }
                    $this->addItem($item);
                }
            }
        }
        return $skippedItems;
    }

    /**
     * Add new product to registry
     *
     * @param int|Mage_Sales_Model_Quote_Item $itemToAdd
     * @param Varien_Object $request
     * @return Enterprise_GiftRegistry_Model_Item
     */
    public function addItem($itemToAdd, $request = null)
    {
        if ($itemToAdd instanceof Mage_Sales_Model_Quote_Item) {
            $productId = $itemToAdd->getProductId();
            $qty = $itemToAdd->getQty();
        } else {
            $productId = $itemToAdd;
            $qty = ($request && $request->getQty()) ? $request->getQty() : 1;
        }
        $product = $this->getProduct($productId);

        if ($product->getTypeInstance()->hasRequiredOptions($product)
            && (!$request && !($itemToAdd instanceof Mage_Sales_Model_Quote_Item))) {
            throw new Mage_Core_Exception(null, self::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS);
        }

        if ($itemToAdd instanceof Mage_Sales_Model_Quote_Item) {
            $cartCandidate = $itemToAdd->getProduct();
            $cartCandidate->setCustomOptions($itemToAdd->getOptionsByCode());
            $cartCandidates = array($cartCandidate);
        } else {
            if (!$request) {
                $request = new Varien_Object();
                $request->setBundleOption(array());//Bundle options mocking for compatibility
            }
            $cartCandidates = $product->getTypeInstance()->prepareForCart($request, $product);
        }

        if (is_string($cartCandidates)) { //prepare process has error, seems like we have bundle
            throw new Mage_Core_Exception($cartCandidates, self::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS);
        }

        $item = Mage::getModel('Enterprise_GiftRegistry_Model_Item');
        $items = $item->getCollection()->addRegistryFilter($this->getId());

        foreach ($cartCandidates as $currentCandidate) {
            if ($currentCandidate->getParentProductId()) {
                continue;
            }
            $alreadyExists = false;
            $productId = $currentCandidate->getId();

            foreach ($items as $itemForCheck) {
                if ($itemForCheck->isRepresentProduct($currentCandidate)) {
                    $alreadyExists = true;
                    $matchedItem = $itemForCheck;
                    break;
                }
            }

            $candidateQty = $currentCandidate->getCartQty();
            if (!empty($candidateQty)) {
                $qty = $candidateQty;
            }

            if ($alreadyExists) {
                $matchedItem->setQty($matchedItem->getQty() + $qty)
                    ->save();
            } else {
                $customOptions = $currentCandidate->getCustomOptions();

                $item = Mage::getModel('Enterprise_GiftRegistry_Model_Item');

                $item->setEntityId($this->getId())
                    ->setProductId($productId)
                    ->setOptions($customOptions)
                    ->setQty($qty)
                    ->save();
            }
        }

        return $item;
    }

    /**
     * Send share email
     *
     * @param string $email
     * @param int $storeId
     * @param string $message
     * @return bool
     */
    public function sendShareRegistryEmail($recipient, $storeId, $message, $sender = null)
    {
        $translate = Mage::getSingleton('Mage_Core_Model_Translate');
        $translate->setTranslateInline(false);

        if (is_null($storeId)) {
            $storeId = $this->getStoreId();
        }
        $store = Mage::app()->getStore($storeId);
        $mail  = Mage::getModel('Mage_Core_Model_Email_Template');

        if (is_array($recipient)) {
            $recipientEmail = $recipient['email'];
            $recipientName = $recipient['name'];
        } else {
            $recipientEmail = $recipient;
            $recipientName = null;
        }

        if (is_array($sender)) {
            $identity = $sender;
        } else {
            $identity = $store->getConfig(self::XML_PATH_SHARE_EMAIL_IDENTITY);
        }

        $templateVars = array(
            'store' => $store,
            'entity' => $this,
            'message' => $message,
            'recipient_name' => $recipientName,
            'url' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->getRegistryLink($this)
        );

        $mail->setDesignConfig(array('area' => 'frontend', 'store' => $storeId));
        $mail->sendTransactional(
            $store->getConfig(self::XML_PATH_SHARE_EMAIL_TEMPLATE),
            $identity,
            $recipientEmail,
            $recipientName,
            $templateVars
        );

        $translate->setTranslateInline(true);

        if ($mail->getSentSuccess()) {
            return true;
        }
        return false;
    }

    /**
     * Send notification to owner on gift registry update -
     * gift registry items or their quantity purchased
     *
     * @param array $updatedQty
     * @return bool
     */
    public function sendUpdateRegistryEmail($updatedQty)
    {
        $translate = Mage::getSingleton('Mage_Core_Model_Translate');
        $translate->setTranslateInline(false);

        $owner = Mage::getModel('Mage_Customer_Model_Customer')
            ->load($this->getCustomerId());

        $store = Mage::app()->getStore();
        $mail = Mage::getModel('Mage_Core_Model_Email_Template');

        $this->setUpdatedQty($updatedQty);

        $templateVars = array(
            'store' => $store,
            'owner' => $owner,
            'entity' => $this
        );

        $mail->setDesignConfig(array('area' => 'frontend', 'store' => $store->getId()));
        $mail->sendTransactional(
            $store->getConfig(self::XML_PATH_UPDATE_EMAIL_TEMPLATE),
            $store->getConfig(self::XML_PATH_UPDATE_EMAIL_IDENTITY),
            $owner->getEmail(),
            $owner->getName(),
            $templateVars
        );

        $translate->setTranslateInline(true);

        if ($mail->getSentSuccess()) {
            return true;
        }
        return false;
    }

    /**
     * Send notification to owner on successful creation of gift registry
     *
     * @return bool
     */
    public function sendNewRegistryEmail()
    {
        $translate = Mage::getSingleton('Mage_Core_Model_Translate');
        $translate->setTranslateInline(false);

        $owner = Mage::getModel('Mage_Customer_Model_Customer')
            ->load($this->getCustomerId());

        $store = Mage::app()->getStore();
        $mail = Mage::getModel('Mage_Core_Model_Email_Template');

        $templateVars = array(
            'store' => $store,
            'owner' => $owner,
            'entity' => $this,
            'url' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->getRegistryLink($this)
        );

        $mail->setDesignConfig(array('area' => 'frontend', 'store' => $store->getId()));
        $mail->sendTransactional(
            $store->getConfig(self::XML_PATH_OWNER_EMAIL_TEMPLATE),
            $store->getConfig(self::XML_PATH_OWNER_EMAIL_IDENTITY),
            $owner->getEmail(),
            $owner->getName(),
            $templateVars
       );

        $translate->setTranslateInline(true);

        if ($mail->getSentSuccess()) {
            return true;
        }
        return false;
    }

    /**
     * Return comma-separated list of entity registrants
     *
     * @return string
     */
    public function getRegistrants()
    {
        $collection = $this->getRegistrantsCollection();
        if ($collection->getSize()) {
            $registrants = array();
            foreach($collection as $item) {
                $registrants[] =  $item->getFirstname().' '.$item->getLastname();
            }
            return implode(', ', $registrants);
        }
        return '';
    }

    /**
     * Return array of entity registrant roles
     *
     * @return string
     */
    public function getRegistrantRoles()
    {
        $collection = $this->getRegistrantsCollection();
        $roles = array();
        if ($collection->getSize()) {
            foreach($collection as $item) {
                $roles[] = $item->getRole();
            }
        }
        return $roles;
    }

    /**
     * Return entity registrants collection
     *
     * @return Enterprise_GiftRegistry_Model_Resource_Person_Collection
     */
    public function getRegistrantsCollection()
    {
        $collection = Mage::getModel('Enterprise_GiftRegistry_Model_Person')->getCollection()
            ->addRegistryFilter($this->getId());

        return $collection;
    }

    /**
     * Return entity items collection
     *
     * @return Enterprise_GiftRegistry_Model_Resource_Item_Collection
     */
    public function getItemsCollection()
    {
        $collection = Mage::getModel('Enterprise_GiftRegistry_Model_Item')->getCollection()
            ->addRegistryFilter($this->getId());
        return $collection;
    }

    /**
     * Load entity model by gift registry item id
     *
     * @param int $itemId
     * @return Enterprise_GiftRegistry_Model_Entity
     */
    public function loadByEntityItem($itemId)
    {
        $this->_getResource()->loadByEntityItem($this, $itemId);
        return $this;
    }

    /**
     * Set active entity
     *
     * @param int $customerId
     * @param int $entityId
     * @return Enterprise_GiftRegistry_Model_Entity
     */
    public function setActiveEntity($customerId, $entityId)
    {
        $this->_getResource()->setActiveEntity($customerId, $entityId);
        return $this;
    }

    /**
     * Return formated address data
     *
     * @return string
     */
    public function getFormatedShippingAddress()
    {
        return $this->exportAddress()->format('html');
    }

    /**
     * Return address object entity on data in GiftRegistry entity
     *
     * @return Mage_Customer_Model_Address
     */
    public function exportAddress()
    {
        $address = Mage::getModel('Mage_Customer_Model_Address');
        $address->setData(unserialize($this->getData('shipping_address')));
        return $address;
    }

     /**
     * Sets up address data to the GiftRegistry entity  object
     *
     * @param Mage_Customer_Model_Address $address
     * @return $this
     */
    public function importAddress(Mage_Customer_Model_Address $address)
    {
        $skip = array('increment_id', 'entity_type_id', 'parent_id', 'entity_id', 'attribute_set_id');
        $data = array();
        $attributes = $address->getAttributes();
        foreach ($attributes as $attribute) {
            if (!in_array($attribute->getAttributeCode(), $skip)) {
                $data[$attribute->getAttributeCode()] = $address->getData($attribute->getAttributeCode());
            }
        }
        $this->setData('shipping_address', serialize($data));
        return $this;
    }

    /**
     * Set type for Model using typeId
     * @param int $typeId
     * @return Enterprise_GiftRegistry_Model_Entity | false
     */
    public function setTypeById($typeId) {
        $this->_typeId = (int) $typeId;
        $this->_type = Mage::getSingleton('Enterprise_GiftRegistry_Model_Type');
        $this->_type->setStoreId(Mage::app()->getStore()->getStoreId());
        $this->setData('type_id', $typeId);
        $this->_type->load($this->_typeId);
        if ($this->_type->getId()) {
            $this->_attributes = $this->_type->getAttributes();
            return $this;
        } else {
            return false;
        }
    }

    /**
     * Get Entity type id
     * @return int|null
     */
    public function getTypeId() {
        return $this->_typeId;
    }

    /**
     * Get Entity type Name
     * @return string|null
     */
    public function getTypeLabel() {
        if ($this->_type !== null) {
            return $this->_type->getLabel();
        }
        return null;
    }

    /**
     * Getter, returns all type custom attributes
     *
     * @return array
     */
    public function getCustomAttributes()
    {
        return $this->_attributes;
    }

    /**
     * Getter, returns all ids for type custom attributes
     *
     * @return array
     */
    public function getStaticTypeIds()
    {
        return Mage::getSingleton('Enterprise_GiftRegistry_Model_Attribute_Config')
            ->getStaticTypesCodes();
    }

    /**
     * Getter, returns registrants custom attributes
     *
     * @return array
     */
    public function getRegistrantAttributes()
    {
        $attributes = $this->getCustomAttributes();
        return is_array($attributes) && !empty($attributes['registrant']) ? $attributes['registrant'] : array();
    }

    /**
     * Getter, return registry attributes
     *
     * @return array
     */
    public function getRegistryAttributes()
    {
        $attributes = $this->getCustomAttributes();
        return is_array($attributes) && !empty($attributes['registry']) ? $attributes['registry'] : array();
    }

    /**
     * Getter, return array of valid values for privacy field
     *
     * @return array
     */
    public function getOptionsIsPublic()
    {
        if (!isset($this->_optionsIsPublic)) {
            $this->_optionsIsPublic = array(
                '0' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Private'),
                '1' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Public'));
        }
        return $this->_optionsIsPublic;
    }

    /**
     * Getter, return array of valid values for status field
     *
     * @return array
     */
    public function getOptionsStatus()
    {
        if (!isset($this->_optionsStatus)) {
            $this->_optionsStatus = array(
                '0' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Inactive'),
                '1' => Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Active'));
        }
        return $this->_optionsStatus;
    }

    /**
     * Validate entity attribute values
     *
     * @return array|bool
     */
    public function validate()
    {
        $errors = array();

        if (!Zend_Validate::is($this->getTitle(), 'NotEmpty')) {
            $errors[] = Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Please enter the title.');
        }

        if (!Zend_Validate::is($this->getMessage(), 'NotEmpty')) {
            $errors[] = Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Please enter the message.');
        }

        if (!Zend_Validate::is($this->getIsPublic(), 'NotEmpty')) {
            $errors[] = Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Please enter correct Privacy setting.');
        } else if (!key_exists($this->getIsPublic(), $this->getOptionsIsPublic())) {
            $errors[] = Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Please enter correct Privacy setting.');
        }

        $allCustomValues = $this->getCustomValues();
        foreach ($this->getStaticTypeIds() as $static) {
            if ($this->hasData($static)) {
                $allCustomValues[$static] = $this->getData($static);
            }
        }

        $errorsCustom = Mage::helper('Enterprise_GiftRegistry_Helper_Data')->validateCustomAttributes(
            $allCustomValues, $this->getRegistryAttributes()
        );
        if ($errorsCustom !== true) {
            $errors = empty($errors) ? $errorsCustom : array_merge($errors, $errorsCustom);
        }
        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Retrieve item product instance
     *
     * @throws Mage_Core_Exception
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct($productId)
    {
        $product = $this->_getData('product');
        if (is_null($product)) {
            if (!$productId) {
                Mage::throwException(Mage::helper('Enterprise_GiftRegistry_Helper_Data')->__('Cannot specify product.'));
            }

            $product = Mage::getModel('Mage_Catalog_Model_Product')
                ->load($productId);

            $this->setData('product', $product);
        }
        return $product;
    }

    /**
     * Import POST data to entity model
     *
     * @param array $data
     * @param bool $isAddAction
     * @return this
     */
    public function importData($data, $isAddAction = true)
    {
        foreach ($this->getStaticTypeIds() as $code){
            if (isset($data[$code])) {
                $this->setData($code, $data[$code]);
            }
        }

        $this->addData(array(
                'is_public' => isset($data['is_public']) ? (int) $data['is_public'] : null,
                'title' => !empty($data['title']) ? $data['title'] : null,
                'message' => !empty($data['message']) ? $data['message'] : null,
                'custom_values' => !empty($data['registry']) ? $data['registry'] : null,
                'is_active' => !empty($data['is_active']) ? $data['is_active'] : 0,
            ));

        if ($isAddAction) {
            $this->addData(array(
                'customer_id' => Mage::getSingleton('Mage_Customer_Model_Session')->getCustomer()->getId(),
                'website_id' => Mage::app()->getStore()->getWebsiteId(),
                'url_key' => $this->getGenerateKeyId(),
                'created_at' => Mage::getModel('Mage_Core_Model_Date')->date(),
                'is_add_action' => true
            ));
        }
        return $this;
    }

    /**
     * Fetches field value from Entity object
     * @param string $field
     * @return mixed
     */
    public function getFieldValue($field)
    {
        $data = $this->getData();
        $value = null;
        if (isset($data[$field])) {
            $value = $data[$field];
        } else if (isset($data['custom_values']) && isset($data['custom_values'][$field])) {
            $value = $data['custom_values'][$field];
        }
        return $value;
    }

    /**
     * Generate uniq url key
     *
     * @return string
     */
    public function getGenerateKeyId()
    {
        return Mage::helper('Mage_Core_Helper_Data')->uniqHash();
    }

    /**
     * Fetch array of custom date types fields id and their format
     *
     * @return array
     */
    public function getDateFieldArray()
    {
        if (!isset($this->_dateFields)) {
            $dateFields = array();
            $attributes = $this->getRegistryAttributes();
            foreach ($attributes as $id => $attribute) {
                if (isset($attribute['type']) && ($attribute['type'] == 'date') && isset($attribute['date_format'])) {
                    $dateFields[$id] = $attribute['date_format'];
                }
            }
            $this->_dateFields = $dateFields;
        }
        return $this->_dateFields;
    }

    /**
     * Custom handler for giftregistry share email action
     *
     * @param Varien_Simplexml_Element $config
     * @param Enterprise_Logging_Model_Event $eventModel
     * @return Enterprise_Logging_Model_Event
     */
    public function postDispatchShare($config, $eventModel, $processor)
    {
        $request = Mage::app()->getRequest();
        $change = Mage::getModel('Enterprise_Logging_Model_Event_Changes');

        $emails = $request->getParam('emails', '');
        if ($emails) {
            $processor->addEventChanges(clone $change->setSourceName('share')
                ->setOriginalData(array())
                ->setResultData(array('emails' => $emails)));
        }

        $message = $request->getParam('message', '');
        if ($emails) {
            $processor->addEventChanges(clone $change->setSourceName('share')
                ->setOriginalData(array())
                ->setResultData(array('message' => $message)));
        }

        return $eventModel;
    }

    /**
     * Load entity model by url key
     *
     * @param string $urlKey
     * @return Enterprise_GiftRegistry_Model_Entity
     */
    public function loadByUrlKey($urlKey)
    {
        $this->_getResource()->loadByUrlKey($this, $urlKey);
        return $this;
    }

    /**
     * Update gift registry items
     *
     * @param array $items
     * @return Enterprise_GiftRegistry_Model_Entity
     */
    public function updateItems($items)
    {
        $this->_getResource()->updateItems($this, $items);
        return $this;
    }
}
