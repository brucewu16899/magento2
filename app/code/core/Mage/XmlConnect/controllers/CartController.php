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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect shopping cart controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_CartController extends Mage_XmlConnect_Controller_Action
{
    /**
     * Shopping cart display action
     */
    public function indexAction()
    {
        $messages = array();
        $cart = $this->_getCart();
        if ($cart->getQuote()->getItemsCount()) {
            $cart->init();
            $cart->save();

            if (!$this->_getQuote()->validateMinimumAmount()) {
                $warning = Mage::getStoreConfig('sales/minimum_order/description');
                $messages[parent::MESSAGE_STATUS_WARNING][] = $warning;
            }
        }

        foreach ($cart->getQuote()->getMessages() as $message) {
            if ($message) {
                $messages[$message->getType()][] = $message->getText();
            }
        }

        /**
         * if customer enteres shopping cart we should mark quote
         * as modified bc he can has checkout page in another window.
         */
        $this->_getSession()->setCartWasUpdated(true);

        $this->loadLayout(false)->getLayout()->getBlock('xmlconnect.cart')->setMessages($messages);
        $this->renderLayout();
    }

    /**
     * Update shoping cart data action
     */
    public function updateAction()
    {
        try {
            $cartData = $this->getRequest()->getParam('cart');
            if (is_array($cartData)) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                foreach ($cartData as $index => $data) {
                    if (isset($data['qty'])) {
                        $cartData[$index]['qty'] = $filter->filter($data['qty']);
                    }
                }
                $cart = $this->_getCart();
                if (! $cart->getCustomerSession()->getCustomer()->getId() && $cart->getQuote()->getCustomerId()) {
                    $cart->getQuote()->setCustomerId(null);
                }
                $cart->updateItems($cartData)
                    ->save();
            }
            $this->_getSession()->setCartWasUpdated(true);
            $this->_message(Mage::helper('xmlconnect')->__('Cart has been updated.'), parent::MESSAGE_STATUS_SUCCESS);
        }
        catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        }
        catch (Exception $e) {
            $this->_message(Mage::helper('xmlconnect')->__('Can\'t update cart.'), self::MESSAGE_STATUS_ERROR);
        }
    }

    /**
     * Get request for product add to cart procedure
     *
     * @param   mixed $requestInfo
     * @return  Varien_Object
     */
    protected function _getProductRequest($requestInfo)
    {
        if ($requestInfo instanceof Varien_Object) {
            $request = $requestInfo;
        } elseif (is_numeric($requestInfo)) {
            $request = new Varien_Object();
            $request->setQty($requestInfo);
        } else {
            $request = new Varien_Object($requestInfo);
        }

        if (!$request->hasQty()) {
            $request->setQty(1);
        }
        return $request;
    }

    /**
     * Add product to shopping cart action
     */
    public function addAction()
    {
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = null;
            $productId = (int) $this->getRequest()->getParam('product');
            if ($productId) {
                $_product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productId);
                if ($_product->getId()) {
                    $product = $_product;
                }
            }
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_message(Mage::helper('xmlconnect')->__('Product is unavailable.'), parent::MESSAGE_STATUS_ERROR);
                return;
            }

            if ($product->isConfigurable()) {

                $request = $this->_getProductRequest($params);
                $cartCandidates = $product->getTypeInstance(true)->prepareForCart($request, $product);
                /**
                 * Hardcoded Configurable product default
                 */
                $minSaleQty = ((isset($params['qty']) ? $params['qty'] : 0) > 1) ? $params['qty'] : 1;
                if (is_array($cartCandidates)) {
                    foreach ($cartCandidates as $candidate) {
                        $current = $candidate->getStockItem()->getMinSaleQty();
                        if ($minSaleQty < $current) {
                            $minSaleQty = $current;
                        }
                    }
                }
                if ($minSaleQty) {
                    $params['qty'] = $minSaleQty;
                }
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                $message = Mage::helper('xmlconnect')->__('%s has been added to your cart.', Mage::helper('core')->htmlEscape($product->getName()));
                if ($cart->getQuote()->getHasError()){
                    $message .= Mage::helper('xmlconnect')->__(' But cart has some errors.');
                }
                $this->_message($message, parent::MESSAGE_STATUS_SUCCESS);
            }
        }
        catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_message($e->getMessage(), parent::MESSAGE_STATUS_ERROR);
            } else {
                $this->_message(implode("\n", array_unique(explode("\n", $e->getMessage()))), parent::MESSAGE_STATUS_ERROR);
            }
        }
        catch (Exception $e) {
            $this->_message(Mage::helper('xmlconnect')->__('Can\'t add item to shopping cart.'), self::MESSAGE_STATUS_ERROR);
        }
    }

    /**
     * Delete shoping cart item action
     */
    public function deleteAction()
    {
        $id = (int) $this->getRequest()->getParam('item_id');
        if ($id) {
            try {
                $this->_getCart()->removeItem($id)->save();
                $this->_message(Mage::helper('xmlconnect')->__('Item has been deleted from cart.'), parent::MESSAGE_STATUS_SUCCESS);
            }
            catch (Mage_Core_Exception $e) {
                $this->_message($e->getMessage(), parent::MESSAGE_STATUS_ERROR);
            }
            catch (Exception $e) {
                $this->_message(Mage::helper('xmlconnect')->__('Can\'t remove the item.'), self::MESSAGE_STATUS_ERROR);
            }
        }
    }

    /**
     * Initialize coupon
     */
    public function couponAction()
    {
        /**
         * No reason continue with empty shopping cart
         */
        if (!$this->_getQuote()->getItemsCount()) {
            $this->_message(Mage::helper('xmlconnect')->__('Shopping cart is empty.'), self::MESSAGE_STATUS_ERROR);
            return;
        }

        $couponCode = (string) $this->getRequest()->getParam('coupon_code');
        if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }
        $oldCouponCode = $this->_getQuote()->getCouponCode();

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            $this->_message(Mage::helper('xmlconnect')->__('Coupon code is empty.'), self::MESSAGE_STATUS_ERROR);
            return;
        }

        try {
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                ->collectTotals()
                ->save();

            if ($couponCode) {
                if ($couponCode == $this->_getQuote()->getCouponCode()) {
                    $this->_message(Mage::helper('xmlconnect')->__('Coupon code %s was applied.', strip_tags($couponCode)), parent::MESSAGE_STATUS_SUCCESS);
                }
                else {
                    $this->_message(Mage::helper('xmlconnect')->__('Coupon code %s is not valid.', strip_tags($couponCode)), self::MESSAGE_STATUS_ERROR);
                }
            } else {
                $this->_message(Mage::helper('xmlconnect')->__('Coupon code was canceled.'), parent::MESSAGE_STATUS_SUCCESS);
            }

        }
        catch (Mage_Core_Exception $e) {
            $this->_message($e->getMessage(), self::MESSAGE_STATUS_ERROR);
        }
        catch (Exception $e) {
            $this->_message(Mage::helper('xmlconnect')->__('Can\'t apply the coupon code.'), self::MESSAGE_STATUS_ERROR);
        }
    }

    /**
     * Get shopping cart summary and flag is_virtual
     */
    public function infoAction()
    {
        $this->_getQuote()->collectTotals()->save();
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Retrieve shopping cart model object
     *
     * @return Mage_Checkout_Model_Cart
     */
    protected function _getCart()
    {
        return Mage::getSingleton('checkout/cart');
    }

    /**
     * Get checkout session model instance
     *
     * @return Mage_Checkout_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current active quote instance
     *
     * @return Mage_Sales_Model_Quote
     */
    protected function _getQuote()
    {
        return $this->_getCart()->getQuote();
    }
}
