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
 * @category   Mage
 * @package    Mage_GoogleAnalytics
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_GoogleAnalytics_Block_Urchin extends Mage_Core_Block_Text 
{
	public function getQuoteOrdersHtml()
	{
		$quote = $this->getQuote();
		if (!$quote) {
			return '';
		}
		
		if ($quote instanceof Mage_Sales_Model_Quote) {
			$quoteId = $quote->getId();
		} else {
			$quoteId = $quote;
		}
		
		if (!$quoteId) {
			return '';
		}
		
		$orders = Mage::getResourceModel('sales/order_collection')	
			->addAttributeToFilter('quote_id', $quoteId)
			->load();
			
		$html = '';
		foreach ($orders as $order) {
			$html .= $this->setOrder($order)->getOrderHtml();
		}
		
		return $html;
	}
	
	public function getOrderHtml()
	{

		$order = $this->getOrder();
		if (!$order) {
			return '';
		}
		
		if (!$order instanceof Mage_Sales_Model_Order) {
			$order = Mage::getModel('sales/order')->load($order);
		}
		
		if (!$order) {
			return '';
		}
		
		$address = $order->getBillingAddress();
		
		$data = array();
		
		$dataArr[] = 'UTM:T'
			.'|'.$order->getIncrementId()
			.'|'.$order->getAffiliation() // empty in our case
			.'|'.$order->getGrandTotal()
			.'|'.$order->getTaxAmount()
			.'|'.$order->getShippingAmount()
			.'|'.$address->getCity()
			.'|'.$address->getRegion()
			.'|'.$address->getCountry();
		
		foreach ($order->getAllItems() as $item) {
			$dataArr[] = 'UTM:I'
				.'|'.$order->getIncrementId()
				.'|'.$item->getSku()
				.'|'.$item->getName()
				.'|'.$item->getCategory() // empty in our case
				.'|'.$item->getPrice()
				.'|'.$item->getQtyOrdered();
		}
		
		$html = '<form style="display:none;" name="utmform"><textarea id="utmtrans">'.join(' ', $dataArr).'</textarea></form>';
		$html.= '<script type="text/javascript">__utmSetTrans();</script>';
		
		return $html;
	}
	
	public function getScriptUrl()
	{
		if (empty($_SERVER['HTTPS']) && $_SERVER['SERVER_PORT']!=Mage::getStoreConfig('web/secure/port')) {
			return 'http://www.google-analytics.com/urchin.js';
		} else {
			return 'https://ssl.google-analytics.com/urchin.js';
		}
	}
	
	public function getAccount()
	{
		if (!$this->hasData('account')) {
			$this->setAccount(Mage::getStoreConfig('web_track/google/urchin_account'));
		}
		return $this->getData('account');
	}
	
	public function getPageName()
	{
		if (!$this->hasData('page_name')) {
			$this->setPageName($this->getRequest()->getPathInfo());
		}
		return $this->getData('page_name');
	}
	
	public function toHtml()
	{
		if (!Mage::getStoreConfig('web_track/google/urchin_enable')) {
			return '';
		}
		
		$this->addText('
<!-- BEGIN GOOGLE ANALYTICS CODE -->
<script src="'.$this->getScriptUrl().'" type="text/javascript"></script> 
<script type="text/javascript">
_uacct="'.$this->getAccount().'";
urchinTracker("'.$this->getPageName().'");
</script>
<!-- END GOOGLE ANALYTICS CODE -->
		');
		
		$this->addText($this->getQuoteOrdersHtml());
		
		return parent::toHtml();
	}
}