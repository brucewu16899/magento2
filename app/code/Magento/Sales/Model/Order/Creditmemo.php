<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Sales\Model\Order;

use Magento\Framework\Api\AttributeDataBuilder;
use Magento\Framework\Model\Exception;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Model\AbstractModel;
use Magento\Sales\Model\EntityInterface;

/**
 * Order creditmemo model
 *
 * @method \Magento\Sales\Model\Resource\Order\Creditmemo _getResource()
 * @method \Magento\Sales\Model\Resource\Order\Creditmemo getResource()
 * @method \Magento\Sales\Model\Order\Creditmemo setStoreId(int $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setBaseShippingTaxAmount(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setStoreToOrderRate(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setBaseDiscountAmount(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setBaseToOrderRate(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setGrandTotal(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setBaseAdjustmentNegative(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setBaseSubtotalInclTax(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setSubtotalInclTax(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setBaseShippingAmount(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setStoreToBaseRate(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setBaseToGlobalRate(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setBaseAdjustment(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setBaseSubtotal(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setDiscountAmount(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setSubtotal(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setAdjustment(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setBaseGrandTotal(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setBaseAdjustmentPositive(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setBaseTaxAmount(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setShippingTaxAmount(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setTaxAmount(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setOrderId(int $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setEmailSent(int $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setCreditmemoStatus(int $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setState(int $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setShippingAddressId(int $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setBillingAddressId(int $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setInvoiceId(int $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setStoreCurrencyCode(string $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setOrderCurrencyCode(string $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setBaseCurrencyCode(string $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setGlobalCurrencyCode(string $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setTransactionId(string $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setIncrementId(string $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setCreatedAt(string $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setUpdatedAt(string $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setHiddenTaxAmount(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setBaseHiddenTaxAmount(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setShippingHiddenTaxAmount(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setBaseShippingHiddenTaxAmnt(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setShippingInclTax(float $value)
 * @method \Magento\Sales\Model\Order\Creditmemo setBaseShippingInclTax(float $value)
 */
class Creditmemo extends AbstractModel implements EntityInterface, CreditmemoInterface
{
    const STATE_OPEN = 1;

    const STATE_REFUNDED = 2;

    const STATE_CANCELED = 3;

    const REPORT_DATE_TYPE_ORDER_CREATED = 'order_created';

    const REPORT_DATE_TYPE_REFUND_CREATED = 'refund_created';

    /*
     * Identifier for order history item
     *
     * @var string
     */
    protected $entityType = 'creditmemo';

    /**
     * @var array
     */
    protected static $_states;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    /**
     * Calculator instances for delta rounding of prices
     *
     * @var array
     */
    protected $_calculators = [];

    /**
     * @var string
     */
    protected $_eventPrefix = 'sales_order_creditmemo';

    /**
     * @var string
     */
    protected $_eventObject = 'creditmemo';

    /**
     * @var \Magento\Sales\Model\Order\Creditmemo\Config
     */
    protected $_creditmemoConfig;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Sales\Model\Resource\Order\Creditmemo\Item\CollectionFactory
     */
    protected $_cmItemCollectionFactory;

    /**
     * @var \Magento\Framework\Math\CalculatorFactory
     */
    protected $_calculatorFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Sales\Model\Order\Creditmemo\CommentFactory
     */
    protected $_commentFactory;

    /**
     * @var \Magento\Sales\Model\Resource\Order\Creditmemo\Comment\CollectionFactory
     */
    protected $_commentCollectionFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\MetadataServiceInterface $metadataService
     * @param AttributeDataBuilder $customAttributeBuilder
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param Creditmemo\Config $creditmemoConfig
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Sales\Model\Resource\Order\Creditmemo\Item\CollectionFactory $cmItemCollectionFactory
     * @param \Magento\Framework\Math\CalculatorFactory $calculatorFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param Creditmemo\CommentFactory $commentFactory
     * @param \Magento\Sales\Model\Resource\Order\Creditmemo\Comment\CollectionFactory $commentCollectionFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\MetadataServiceInterface $metadataService,
        AttributeDataBuilder $customAttributeBuilder,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Sales\Model\Order\Creditmemo\Config $creditmemoConfig,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Resource\Order\Creditmemo\Item\CollectionFactory $cmItemCollectionFactory,
        \Magento\Framework\Math\CalculatorFactory $calculatorFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\Order\Creditmemo\CommentFactory $commentFactory,
        \Magento\Sales\Model\Resource\Order\Creditmemo\Comment\CollectionFactory $commentCollectionFactory,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = []
    ) {
        $this->_creditmemoConfig = $creditmemoConfig;
        $this->_orderFactory = $orderFactory;
        $this->_cmItemCollectionFactory = $cmItemCollectionFactory;
        $this->_calculatorFactory = $calculatorFactory;
        $this->_storeManager = $storeManager;
        $this->_commentFactory = $commentFactory;
        $this->_commentCollectionFactory = $commentCollectionFactory;
        $this->priceCurrency = $priceCurrency;
        parent::__construct(
            $context,
            $registry,
            $metadataService,
            $customAttributeBuilder,
            $localeDate,
            $dateTime,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Initialize creditmemo resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Resource\Order\Creditmemo');
    }

    /**
     * Retrieve Creditmemo configuration model
     *
     * @return \Magento\Sales\Model\Order\Creditmemo\Config
     */
    public function getConfig()
    {
        return $this->_creditmemoConfig;
    }

    /**
     * Retrieve creditmemo store instance
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->getOrder()->getStore();
    }

    /**
     * Declare order for creditmemo
     *
     * @param \Magento\Sales\Model\Order $order
     * @return $this
     */
    public function setOrder(\Magento\Sales\Model\Order $order)
    {
        $this->_order = $order;
        $this->setOrderId($order->getId())->setStoreId($order->getStoreId());
        return $this;
    }

    /**
     * Retrieve the order the creditmemo for created for
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        if (!$this->_order instanceof \Magento\Sales\Model\Order) {
            $this->_order = $this->_orderFactory->create()->load($this->getOrderId());
        }
        return $this->_order->setHistoryEntityName($this->entityType);
    }

    /**
     * Return order entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return $this->entityType;
    }

    /**
     * Retrieve billing address
     *
     * @return Address
     */
    public function getBillingAddress()
    {
        return $this->getOrder()->getBillingAddress();
    }

    /**
     * Retrieve shipping address
     *
     * @return Address
     */
    public function getShippingAddress()
    {
        return $this->getOrder()->getShippingAddress();
    }

    /**
     * @return mixed
     */
    public function getItemsCollection()
    {
        $collection = $this->_cmItemCollectionFactory->create()->setCreditmemoFilter($this->getId());
        if ($this->getId()) {
            foreach ($collection as $item) {
                $item->setCreditmemo($this);
            }
        }
        return $collection;
    }

    /**
     * @return \Magento\Sales\Model\Order\Creditmemo\Item[]
     */
    public function getAllItems()
    {
        $items = [];
        foreach ($this->getItems() as $item) {
            if (!$item->isDeleted()) {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * @param mixed $itemId
     * @return mixed
     */
    public function getItemById($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId() == $itemId) {
                return $item;
            }
        }
        return false;
    }

    /**
     * Returns credit memo item by its order id
     *
     * @param mixed $orderId
     * @return \Magento\Sales\Model\Order\Creditmemo\Item|bool
     */
    public function getItemByOrderId($orderId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getOrderItemId() == $orderId) {
                return $item;
            }
        }
        return false;
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo\Item $item
     * @return $this
     */
    public function addItem(\Magento\Sales\Model\Order\Creditmemo\Item $item)
    {
        $item->setCreditmemo($this)->setParentId($this->getId())->setStoreId($this->getStoreId());
        if (!$item->getId()) {
            $this->setItems(array_merge($this->getItems(), [$item]));
        }
        return $this;
    }

    /**
     * Creditmemo totals collecting
     *
     * @return $this
     */
    public function collectTotals()
    {
        foreach ($this->getConfig()->getTotalModels() as $model) {
            $model->collect($this);
        }
        return $this;
    }

    /**
     * Round price considering delta
     *
     * @param float $price
     * @param string $type
     * @param bool $negative Indicates if we perform addition (true) or subtraction (false) of rounded value
     * @return float
     */
    public function roundPrice($price, $type = 'regular', $negative = false)
    {
        if ($price) {
            if (!isset($this->_calculators[$type])) {
                $this->_calculators[$type] = $this->_calculatorFactory->create(['scope' => $this->getStore()]);
            }
            $price = $this->_calculators[$type]->deltaRound($price, $negative);
        }
        return $price;
    }

    /**
     * @return bool
     */
    public function canRefund()
    {
        if ($this->getState() != self::STATE_CANCELED &&
            $this->getState() != self::STATE_REFUNDED &&
            $this->getOrder()->getPayment()->canRefund()
        ) {
            return true;
        }
        return false;
    }

    /**
     * Check creditmemo cancel action availability
     *
     * @return bool
     */
    public function canCancel()
    {
        return $this->getState() == self::STATE_OPEN;
    }

    /**
     * Check invoice void action availability
     *
     * @return bool
     */
    public function canVoid()
    {
        return false;
        $canVoid = false;
        if ($this->getState() == self::STATE_REFUNDED) {
            $canVoid = $this->getCanVoidFlag();
            /**
             * If we not retrieve negative answer from payment yet
             */
            if (is_null($canVoid)) {
                $canVoid = $this->getOrder()->getPayment()->canVoid($this);
                if ($canVoid === false) {
                    $this->setCanVoidFlag(false);
                    $this->_saveBeforeDestruct = true;
                }
            } else {
                $canVoid = (bool)$canVoid;
            }
        }
        return $canVoid;
    }

    /**
     * @return $this
     * @throws Exception
     */
    public function refund()
    {
        $this->setState(self::STATE_REFUNDED);
        $orderRefund = $this->priceCurrency->round(
            $this->getOrder()->getTotalRefunded() + $this->getGrandTotal()
        );
        $baseOrderRefund = $this->priceCurrency->round(
            $this->getOrder()->getBaseTotalRefunded() + $this->getBaseGrandTotal()
        );

        if ($baseOrderRefund > $this->priceCurrency->round($this->getOrder()->getBaseTotalPaid())) {
            $baseAvailableRefund = $this->getOrder()->getBaseTotalPaid() - $this->getOrder()->getBaseTotalRefunded();

            throw new Exception(
                __(
                    'The most money available to refund is %1.',
                    $this->getOrder()->formatBasePrice($baseAvailableRefund)
                )
            );
        }
        $order = $this->getOrder();
        $order->setBaseTotalRefunded($baseOrderRefund);
        $order->setTotalRefunded($orderRefund);

        $order->setBaseSubtotalRefunded($order->getBaseSubtotalRefunded() + $this->getBaseSubtotal());
        $order->setSubtotalRefunded($order->getSubtotalRefunded() + $this->getSubtotal());

        $order->setBaseTaxRefunded($order->getBaseTaxRefunded() + $this->getBaseTaxAmount());
        $order->setTaxRefunded($order->getTaxRefunded() + $this->getTaxAmount());
        $order->setBaseHiddenTaxRefunded($order->getBaseHiddenTaxRefunded() + $this->getBaseHiddenTaxAmount());
        $order->setHiddenTaxRefunded($order->getHiddenTaxRefunded() + $this->getHiddenTaxAmount());

        $order->setBaseShippingRefunded($order->getBaseShippingRefunded() + $this->getBaseShippingAmount());
        $order->setShippingRefunded($order->getShippingRefunded() + $this->getShippingAmount());

        $order->setBaseShippingTaxRefunded($order->getBaseShippingTaxRefunded() + $this->getBaseShippingTaxAmount());
        $order->setShippingTaxRefunded($order->getShippingTaxRefunded() + $this->getShippingTaxAmount());

        $order->setAdjustmentPositive($order->getAdjustmentPositive() + $this->getAdjustmentPositive());
        $order->setBaseAdjustmentPositive($order->getBaseAdjustmentPositive() + $this->getBaseAdjustmentPositive());

        $order->setAdjustmentNegative($order->getAdjustmentNegative() + $this->getAdjustmentNegative());
        $order->setBaseAdjustmentNegative($order->getBaseAdjustmentNegative() + $this->getBaseAdjustmentNegative());

        $order->setDiscountRefunded($order->getDiscountRefunded() + $this->getDiscountAmount());
        $order->setBaseDiscountRefunded($order->getBaseDiscountRefunded() + $this->getBaseDiscountAmount());

        if ($this->getInvoice()) {
            $this->getInvoice()->setIsUsedForRefund(true);
            $this->getInvoice()->setBaseTotalRefunded(
                $this->getInvoice()->getBaseTotalRefunded() + $this->getBaseGrandTotal()
            );
            $this->setInvoiceId($this->getInvoice()->getId());
        }

        if (!$this->getPaymentRefundDisallowed()) {
            $order->getPayment()->refund($this);
        }

        $this->_eventManager->dispatch('sales_order_creditmemo_refund', [$this->_eventObject => $this]);
        return $this;
    }

    /**
     * Cancel Creditmemo action
     *
     * @return $this
     */
    public function cancel()
    {
        $this->setState(self::STATE_CANCELED);
        foreach ($this->getAllItems() as $item) {
            $item->cancel();
        }
        $this->getOrder()->getPayment()->cancelCreditmemo($this);

        if ($this->getTransactionId()) {
            $this->getOrder()->setTotalOnlineRefunded(
                $this->getOrder()->getTotalOnlineRefunded() - $this->getGrandTotal()
            );
            $this->getOrder()->setBaseTotalOnlineRefunded(
                $this->getOrder()->getBaseTotalOnlineRefunded() - $this->getBaseGrandTotal()
            );
        } else {
            $this->getOrder()->setTotalOfflineRefunded(
                $this->getOrder()->getTotalOfflineRefunded() - $this->getGrandTotal()
            );
            $this->getOrder()->setBaseTotalOfflineRefunded(
                $this->getOrder()->getBaseTotalOfflineRefunded() - $this->getBaseGrandTotal()
            );
        }

        $this->getOrder()->setBaseSubtotalRefunded(
            $this->getOrder()->getBaseSubtotalRefunded() - $this->getBaseSubtotal()
        );
        $this->getOrder()->setSubtotalRefunded($this->getOrder()->getSubtotalRefunded() - $this->getSubtotal());

        $this->getOrder()->setBaseTaxRefunded($this->getOrder()->getBaseTaxRefunded() - $this->getBaseTaxAmount());
        $this->getOrder()->setTaxRefunded($this->getOrder()->getTaxRefunded() - $this->getTaxAmount());

        $this->getOrder()->setBaseShippingRefunded(
            $this->getOrder()->getBaseShippingRefunded() - $this->getBaseShippingAmount()
        );
        $this->getOrder()->setShippingRefunded($this->getOrder()->getShippingRefunded() - $this->getShippingAmount());

        $this->_eventManager->dispatch('sales_order_creditmemo_cancel', [$this->_eventObject => $this]);
        return $this;
    }

    /**
     * Register creditmemo
     *
     * Apply to order, order items etc.
     *
     * @return $this
     * @throws Exception
     */
    public function register()
    {
        if ($this->getId()) {
            throw new Exception(__('We cannot register an existing credit memo.'));
        }

        foreach ($this->getAllItems() as $item) {
            if ($item->getQty() > 0) {
                $item->register();
            } else {
                $item->isDeleted(true);
            }
        }

        $this->setDoTransaction(true);
        if ($this->getOfflineRequested()) {
            $this->setDoTransaction(false);
        }
        $this->refund();

        if ($this->getDoTransaction()) {
            $this->getOrder()->setTotalOnlineRefunded(
                $this->getOrder()->getTotalOnlineRefunded() + $this->getGrandTotal()
            );
            $this->getOrder()->setBaseTotalOnlineRefunded(
                $this->getOrder()->getBaseTotalOnlineRefunded() + $this->getBaseGrandTotal()
            );
        } else {
            $this->getOrder()->setTotalOfflineRefunded(
                $this->getOrder()->getTotalOfflineRefunded() + $this->getGrandTotal()
            );
            $this->getOrder()->setBaseTotalOfflineRefunded(
                $this->getOrder()->getBaseTotalOfflineRefunded() + $this->getBaseGrandTotal()
            );
        }

        $this->getOrder()->setBaseTotalInvoicedCost(
            $this->getOrder()->getBaseTotalInvoicedCost() - $this->getBaseCost()
        );

        $state = $this->getState();
        if (is_null($state)) {
            $this->setState(self::STATE_OPEN);
        }
        return $this;
    }

    /**
     * Retrieve Creditmemo states array
     *
     * @return array
     */
    public static function getStates()
    {
        if (is_null(self::$_states)) {
            self::$_states = [
                self::STATE_OPEN => __('Pending'),
                self::STATE_REFUNDED => __('Refunded'),
                self::STATE_CANCELED => __('Canceled'),
            ];
        }
        return self::$_states;
    }

    /**
     * Retrieve Creditmemo state name by state identifier
     *
     * @param   int $stateId
     * @return  string
     */
    public function getStateName($stateId = null)
    {
        if (is_null($stateId)) {
            $stateId = $this->getState();
        }

        if (is_null(self::$_states)) {
            self::getStates();
        }
        if (isset(self::$_states[$stateId])) {
            return self::$_states[$stateId];
        }
        return __('Unknown State');
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function setShippingAmount($amount)
    {
        // base shipping amount calculated in total model
        //        $amount = $this->getStore()->round($amount);
        //        $this->setData('base_shipping_amount', $amount);
        //
        //        $amount = $this->getStore()->round(
        //            $amount*$this->getOrder()->getStoreToOrderRate()
        //        );
        $this->setData('shipping_amount', $amount);
        return $this;
    }

    /**
     * @param string $amount
     * @return $this
     */
    public function setAdjustmentPositive($amount)
    {
        $amount = trim($amount);
        if (substr($amount, -1) == '%') {
            $amount = (double)substr($amount, 0, -1);
            $amount = $this->getOrder()->getGrandTotal() * $amount / 100;
        }

        $amount = $this->priceCurrency->round($amount);
        $this->setData('base_adjustment_positive', $amount);

        $amount = $this->priceCurrency->round($amount * $this->getOrder()->getBaseToOrderRate());
        $this->setData('adjustment_positive', $amount);
        return $this;
    }

    /**
     * @param string $amount
     * @return $this
     */
    public function setAdjustmentNegative($amount)
    {
        $amount = trim($amount);
        if (substr($amount, -1) == '%') {
            $amount = (double)substr($amount, 0, -1);
            $amount = $this->getOrder()->getGrandTotal() * $amount / 100;
        }

        $amount = $this->priceCurrency->round($amount);
        $this->setData('base_adjustment_negative', $amount);

        $amount = $this->priceCurrency->round($amount * $this->getOrder()->getBaseToOrderRate());
        $this->setData('adjustment_negative', $amount);
        return $this;
    }

    /**
     * Checking if the creditmemo is last
     *
     * @return bool
     */
    public function isLast()
    {
        foreach ($this->getAllItems() as $item) {
            if (!$item->isLast()) {
                return false;
            }
        }
        return true;
    }

    /**
     * Adds comment to credit memo with additional possibility to send it to customer via email
     * and show it in customer account
     *
     * @param \Magento\Sales\Model\Order\Creditmemo\Comment|string $comment
     * @param bool $notify
     * @param bool $visibleOnFront
     *
     * @return \Magento\Sales\Model\Order\Creditmemo\Comment
     */
    public function addComment($comment, $notify = false, $visibleOnFront = false)
    {
        if (!$comment instanceof \Magento\Sales\Model\Order\Creditmemo\Comment) {
            $comment = $this->_commentFactory->create()->setComment(
                $comment
            )->setIsCustomerNotified(
                $notify
            )->setIsVisibleOnFront(
                $visibleOnFront
            );
        }
        $comment->setCreditmemo($this)->setParentId($this->getId())->setStoreId($this->getStoreId());
        $this->setComments(array_merge($this->getComments(), [$comment]));
        return $comment;
    }

    /**
     * @param bool $reload
     * @return \Magento\Sales\Model\Resource\Order\Creditmemo\Comment\Collection
     */
    public function getCommentsCollection($reload = false)
    {
        $collection = $this->_commentCollectionFactory->create()->setCreditmemoFilter($this->getId())
            ->setCreatedAtOrder();
//
//            $this->setComments($comments);
//            /**
//             * When credit memo created with adding comment,
//             * comments collection must be loaded before we added this comment.
//             */
//            $this->getComments()->load();

        if ($this->getId()) {
            foreach ($collection as $comment) {
                $comment->setCreditmemo($this);
            }
        }
        return $collection;
    }

    /**
     * Get creditmemos collection filtered by $filter
     *
     * @param array|null $filter
     * @return \Magento\Sales\Model\Resource\Order\Creditmemo\Collection
     */
    public function getFilteredCollectionItems($filter = null)
    {
        return $this->getResourceCollection()->getFiltered($filter);
    }

    /**
     * Returns increment id
     *
     * @return string
     */
    public function getIncrementId()
    {
        return $this->getData('increment_id');
    }

    /**
     * @return bool
     */
    public function isValidGrandTotal()
    {
        return !($this->getGrandTotal() <= 0 && !$this->getAllowZeroGrandTotal());
    }

    /**
     * Returns discount_description
     *
     * @return string
     */
    public function getDiscountDescription()
    {
        return $this->getData(CreditmemoInterface::DISCOUNT_DESCRIPTION);
    }

    /**
     * Return creditmemo items
     *
     * @return \Magento\Sales\Api\Data\CreditmemoItemInterface[]
     */
    public function getItems()
    {
        if ($this->getData(CreditmemoInterface::ITEMS) == null) {
            $this->setData(
                CreditmemoInterface::ITEMS,
                $this->getItemsCollection()->getItems()
            );
        }
        return $this->getData(CreditmemoInterface::ITEMS);
    }

    /**
     * Returns adjustment
     *
     * @return float
     */
    public function getAdjustment()
    {
        return $this->getData(CreditmemoInterface::ADJUSTMENT);
    }

    /**
     * Returns adjustment_negative
     *
     * @return float
     */
    public function getAdjustmentNegative()
    {
        return $this->getData(CreditmemoInterface::ADJUSTMENT_NEGATIVE);
    }

    /**
     * Returns adjustment_positive
     *
     * @return float
     */
    public function getAdjustmentPositive()
    {
        return $this->getData(CreditmemoInterface::ADJUSTMENT_POSITIVE);
    }

    /**
     * Returns base_adjustment
     *
     * @return float
     */
    public function getBaseAdjustment()
    {
        return $this->getData(CreditmemoInterface::BASE_ADJUSTMENT);
    }

    /**
     * Returns base_adjustment_negative
     *
     * @return float
     */
    public function getBaseAdjustmentNegative()
    {
        return $this->getData(CreditmemoInterface::BASE_ADJUSTMENT_NEGATIVE);
    }

    /**
     * Returns base_adjustment_positive
     *
     * @return float
     */
    public function getBaseAdjustmentPositive()
    {
        return $this->getData(CreditmemoInterface::BASE_ADJUSTMENT_POSITIVE);
    }

    /**
     * Returns base_currency_code
     *
     * @return string
     */
    public function getBaseCurrencyCode()
    {
        return $this->getData(CreditmemoInterface::BASE_CURRENCY_CODE);
    }

    /**
     * Returns base_discount_amount
     *
     * @return float
     */
    public function getBaseDiscountAmount()
    {
        return $this->getData(CreditmemoInterface::BASE_DISCOUNT_AMOUNT);
    }

    /**
     * Returns base_grand_total
     *
     * @return float
     */
    public function getBaseGrandTotal()
    {
        return $this->getData(CreditmemoInterface::BASE_GRAND_TOTAL);
    }

    /**
     * Returns base_hidden_tax_amount
     *
     * @return float
     */
    public function getBaseHiddenTaxAmount()
    {
        return $this->getData(CreditmemoInterface::BASE_HIDDEN_TAX_AMOUNT);
    }

    /**
     * Returns base_shipping_amount
     *
     * @return float
     */
    public function getBaseShippingAmount()
    {
        return $this->getData(CreditmemoInterface::BASE_SHIPPING_AMOUNT);
    }

    /**
     * Returns base_shipping_hidden_tax_amnt
     *
     * @return float
     */
    public function getBaseShippingHiddenTaxAmnt()
    {
        return $this->getData(CreditmemoInterface::BASE_SHIPPING_HIDDEN_TAX_AMNT);
    }

    /**
     * Returns base_shipping_incl_tax
     *
     * @return float
     */
    public function getBaseShippingInclTax()
    {
        return $this->getData(CreditmemoInterface::BASE_SHIPPING_INCL_TAX);
    }

    /**
     * Returns base_shipping_tax_amount
     *
     * @return float
     */
    public function getBaseShippingTaxAmount()
    {
        return $this->getData(CreditmemoInterface::BASE_SHIPPING_TAX_AMOUNT);
    }

    /**
     * Returns base_subtotal
     *
     * @return float
     */
    public function getBaseSubtotal()
    {
        return $this->getData(CreditmemoInterface::BASE_SUBTOTAL);
    }

    /**
     * Returns base_subtotal_incl_tax
     *
     * @return float
     */
    public function getBaseSubtotalInclTax()
    {
        return $this->getData(CreditmemoInterface::BASE_SUBTOTAL_INCL_TAX);
    }

    /**
     * Returns base_tax_amount
     *
     * @return float
     */
    public function getBaseTaxAmount()
    {
        return $this->getData(CreditmemoInterface::BASE_TAX_AMOUNT);
    }

    /**
     * Returns base_to_global_rate
     *
     * @return float
     */
    public function getBaseToGlobalRate()
    {
        return $this->getData(CreditmemoInterface::BASE_TO_GLOBAL_RATE);
    }

    /**
     * Returns base_to_order_rate
     *
     * @return float
     */
    public function getBaseToOrderRate()
    {
        return $this->getData(CreditmemoInterface::BASE_TO_ORDER_RATE);
    }

    /**
     * Returns billing_address_id
     *
     * @return int
     */
    public function getBillingAddressId()
    {
        return $this->getData(CreditmemoInterface::BILLING_ADDRESS_ID);
    }

    /**
     * Returns created_at
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(CreditmemoInterface::CREATED_AT);
    }

    /**
     * Returns creditmemo_status
     *
     * @return int
     */
    public function getCreditmemoStatus()
    {
        return $this->getData(CreditmemoInterface::CREDITMEMO_STATUS);
    }

    /**
     * Returns discount_amount
     *
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->getData(CreditmemoInterface::DISCOUNT_AMOUNT);
    }

    /**
     * Returns email_sent
     *
     * @return int
     */
    public function getEmailSent()
    {
        return $this->getData(CreditmemoInterface::EMAIL_SENT);
    }

    /**
     * Returns global_currency_code
     *
     * @return string
     */
    public function getGlobalCurrencyCode()
    {
        return $this->getData(CreditmemoInterface::GLOBAL_CURRENCY_CODE);
    }

    /**
     * Returns grand_total
     *
     * @return float
     */
    public function getGrandTotal()
    {
        return $this->getData(CreditmemoInterface::GRAND_TOTAL);
    }

    /**
     * Returns hidden_tax_amount
     *
     * @return float
     */
    public function getHiddenTaxAmount()
    {
        return $this->getData(CreditmemoInterface::HIDDEN_TAX_AMOUNT);
    }

    /**
     * Returns invoice_id
     *
     * @return int
     */
    public function getInvoiceId()
    {
        return $this->getData(CreditmemoInterface::INVOICE_ID);
    }

    /**
     * Returns order_currency_code
     *
     * @return string
     */
    public function getOrderCurrencyCode()
    {
        return $this->getData(CreditmemoInterface::ORDER_CURRENCY_CODE);
    }

    /**
     * Returns order_id
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData(CreditmemoInterface::ORDER_ID);
    }

    /**
     * Returns shipping_address_id
     *
     * @return int
     */
    public function getShippingAddressId()
    {
        return $this->getData(CreditmemoInterface::SHIPPING_ADDRESS_ID);
    }

    /**
     * Returns shipping_amount
     *
     * @return float
     */
    public function getShippingAmount()
    {
        return $this->getData(CreditmemoInterface::SHIPPING_AMOUNT);
    }

    /**
     * Returns shipping_hidden_tax_amount
     *
     * @return float
     */
    public function getShippingHiddenTaxAmount()
    {
        return $this->getData(CreditmemoInterface::SHIPPING_HIDDEN_TAX_AMOUNT);
    }

    /**
     * Returns shipping_incl_tax
     *
     * @return float
     */
    public function getShippingInclTax()
    {
        return $this->getData(CreditmemoInterface::SHIPPING_INCL_TAX);
    }

    /**
     * Returns shipping_tax_amount
     *
     * @return float
     */
    public function getShippingTaxAmount()
    {
        return $this->getData(CreditmemoInterface::SHIPPING_TAX_AMOUNT);
    }

    /**
     * Returns state
     *
     * @return int
     */
    public function getState()
    {
        return $this->getData(CreditmemoInterface::STATE);
    }

    /**
     * Returns store_currency_code
     *
     * @return string
     */
    public function getStoreCurrencyCode()
    {
        return $this->getData(CreditmemoInterface::STORE_CURRENCY_CODE);
    }

    /**
     * Returns store_id
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->getData(CreditmemoInterface::STORE_ID);
    }

    /**
     * Returns store_to_base_rate
     *
     * @return float
     */
    public function getStoreToBaseRate()
    {
        return $this->getData(CreditmemoInterface::STORE_TO_BASE_RATE);
    }

    /**
     * Returns store_to_order_rate
     *
     * @return float
     */
    public function getStoreToOrderRate()
    {
        return $this->getData(CreditmemoInterface::STORE_TO_ORDER_RATE);
    }

    /**
     * Returns subtotal
     *
     * @return float
     */
    public function getSubtotal()
    {
        return $this->getData(CreditmemoInterface::SUBTOTAL);
    }

    /**
     * Returns subtotal_incl_tax
     *
     * @return float
     */
    public function getSubtotalInclTax()
    {
        return $this->getData(CreditmemoInterface::SUBTOTAL_INCL_TAX);
    }

    /**
     * Returns tax_amount
     *
     * @return float
     */
    public function getTaxAmount()
    {
        return $this->getData(CreditmemoInterface::TAX_AMOUNT);
    }

    /**
     * Returns transaction_id
     *
     * @return string
     */
    public function getTransactionId()
    {
        return $this->getData(CreditmemoInterface::TRANSACTION_ID);
    }

    /**
     * Returns updated_at
     *
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData(CreditmemoInterface::UPDATED_AT);
    }

    /**
     * Return creditmemo comments
     *
     * @return \Magento\Sales\Api\Data\CreditmemoCommentInterface[]|null
     */
    public function getComments()
    {
        if ($this->getData(CreditmemoInterface::COMMENTS) == null) {
            $this->setData(
                CreditmemoInterface::COMMENTS,
                $this->getCommentsCollection()->getItems()
            );
        }
        return $this->getData(CreditmemoInterface::COMMENTS);
    }
}
