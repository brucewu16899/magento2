<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

class Mage_AdminNotification_Model_Resource_System_Message_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * System message list
     *
     * @var Mage_AdminNotification_Model_System_MessageList
     */
    protected $_messageList;

    /**
     * Number of messages by severity
     *
     * @var array
     */
    protected $_countBySeverity = array();

    /**
     * @param Varien_Data_Collection_Db_FetchStrategyInterface $fetchStrategy
     * @param Mage_AdminNotification_Model_System_MessageList $messageList
     * @param Mage_Core_Model_Resource_Db_Abstract $resource
     */
    public function __construct(
        Varien_Data_Collection_Db_FetchStrategyInterface $fetchStrategy,
        Mage_AdminNotification_Model_System_MessageList $messageList,
        Mage_Core_Model_Resource_Db_Abstract $resource = null
    ) {
        $this->_messageList = $messageList;
        parent::__construct($fetchStrategy, $resource);
    }

    /**
     * Resource collection initialization
     */
    protected function _construct()
    {
        $this->_init(
            'Mage_AdminNotification_Model_System_Message', 'Mage_AdminNotification_Model_Resource_System_Message'
        );
    }

    /**
     * Initialize db query
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract|void
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addOrder('severity', self::SORT_ORDER_ASC)
            ->addOrder('created_at');
    }

    /**
     * Initialize system messages after load
     *
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    protected function _afterLoad()
    {
        foreach ($this->_items as $key => $item) {
            $message = $this->_messageList->getMessageByIdentity($item->getIdentity());
            if ($message) {
                $item->setText($message->getText());
                if (array_key_exists($message->getSeverity(), $this->_countBySeverity)) {
                    $this->_countBySeverity[$message->getSeverity()]++;
                } else {
                    $this->_countBySeverity[$message->getSeverity()] = 1;
                }
            } else {
                unset($this->_items[$key]);
            }
        }
    }

    /**
     * Set message severity filter
     *
     * @param int $severity
     * @return Mage_Core_Model_Resource_Db_Abstract
     */
    public function setSeverity($severity)
    {
        $this->addFieldToFilter('severity', array('eq' => $severity * 1));
        return $this;
    }

    /**
     * Retrieve number of messages by severity
     *
     * @param int $severity
     * @return int
     */
    public function getCountBySeverity($severity)
    {
        return isset($this->_countBySeverity[$severity]) ? $this->_countBySeverity[$severity] : 0;
    }
}
