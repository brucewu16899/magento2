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
 * @package    Mage_Rss
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review form block
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author     Lindy Kyaw <lindy@varien.com>
 */
class Mage_Rss_Block_Catalog extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
        $type = $this->getType() ? $this->getType() : 'new';
        if ($type=='special') {
            return $this->specialProductHtml();
        } elseif ($type=='salesrule') {
            return $this->salesruleProductHtml();
        } elseif ($type=='tag') {
            return $this->taggedProductHtml();
        } else {
            return $this->newProductHtml();
        }
        //return $this->getType();
    }

    protected function newProductHtml()
    {
        //store id is store view id
        $storeId =   (int) $this->getRequest()->getParam('sid');
        if($storeId == null) {
           $storeId = Mage::app()->getStore()->getId();
        }

        $url = Mage::getUrl('');
        $newurl = Mage::getUrl('rss/catalog/new');
        $title = Mage::helper('rss')->__('%s - New Products',Mage::app()->getStore()->getName());
        $lang = Mage::getStoreConfig('general/locale/code');

        $xmlStr = <<< EOT
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
<channel>
    <link>{$newurl}</link>
    <title>{$title}</title>
    <description>{$title}</description>
EOT;
/*
oringinal price - getPrice() - inputed in admin
special price - getSpecialPrice()
getFinalPrice() - used in shopping cart calculations
*/
        $today_date = Mage::getSingleton('core/date')->date();

        $products = Mage::getModel('catalog/product')
            ->setStoreId($storeId)
            ->getCollection()
            ->addAttributeToFilter('news_from_date', array('date'=>true, 'to'=> $today_date))
            ->addAttributeToFilter(array(array('attribute'=>'news_to_date', 'date'=>true, 'from'=>$today_date), array('attribute'=>'news_to_date', 'is' => new Zend_Db_Expr('null'))),'','left')
            ->addAttributeToSort('news_from_date','desc')
            ;
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);

        $product = Mage::getModel('catalog/product');

        /*
        using resource iterator to load the data one by one
        instead of loading all at the same time. loading all data at the same time can cause the big memory allocation.
        */
        Mage::getSingleton('core/resource_iterator')
            ->walk($products->getSelect(), array(array($this, 'addNewItemXml')), array('xml'=>&$xmlStr, 'product'=>$product));

        $xmlStr .= "
</channel>
</rss>";
        return $xmlStr;
    }

    public function addNewItemXml($args)
    {
        $product = $args['product'];
        $product->unsetData()->load($args['row']['entity_id']);
        $description = '<table><tr>'.
        '<td><a href="'.$product->getProductUrl().'"><img src="'.$product->getThumbnailUrl().'" border="0" align="left" height="75" width="75"></a></td>'.
        '<td  style="text-decoration:none;">'.$product->getDescription().
        '<p> Price:'.Mage::helper('core')->currency($product->getPrice()).
        ($product->getPrice() != $product->getFinalPrice() ? ' Special Price:'. Mage::helper('core')->currency($product->getFinalPrice()) : '').
        '</p>'.
        '</td>'.
        '</tr></table>';

        $args['xml'] .= <<< EOT
<item>
    <title><![CDATA[{$product->getName()}]]></title>
    <description><![CDATA[{$description}]]></description>
    <link><![CDATA[{$product->getProductUrl()}]]></link>
</item>
EOT;
    }

    protected function salesruleProductHtml()
    {
        //store id is store view id
        $storeId =   (int) $this->getRequest()->getParam('sid');
        if($storeId == null) {
           $storeId = Mage::app()->getStore()->getId();
        }

        //customer group id
        $custGroup =   (int) $this->getRequest()->getParam('cid');
        if($custGroup == null) {
            $custGroup = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }

        $url = Mage::getUrl('');
        $newurl = Mage::getUrl('rss/catalog/new');
        $title = Mage::helper('rss')->__('%s - Discounts and Coupons',Mage::app()->getStore($storeId)->getName());
        $lang = Mage::getStoreConfig('general/locale/code');

        $xmlStr = <<< EOT
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
<channel>
    <link>{$newurl}</link>
    <title>{$title}</title>
    <description>{$title}</description>
EOT;
        $today_date = Mage::getSingleton('core/date')->date();
        $_saleRule = Mage::getModel('salesrule/rule');
        $collection = $_saleRule->getResourceCollection()
                    ->addFieldToFilter('from_date', array('date'=>true, 'to' => $today_date))
        			->addFieldToFilter('to_date', array('date'=>true, 'from' => $today_date))
        			->addFieldToFilter('store_ids',array('finset' => $storeId))
        			->addFieldToFilter('customer_group_ids', array('finset' => $custGroup))
        			->addFieldToFilter('is_rss',1)
        			->setOrder('from_date','desc');

        $collection->load();

        foreach ($collection as $sr) {
            $description = '<table><tr>'.
            '<td style="text-decoration:none;">'.$sr->getDescription().
            '<br/>Discount Start Date: '.$this->formatDate($sr->getFromDate(), 'medium').
            '<br/>Discount End Date: '.$this->formatDate($sr->getToDate(), 'medium').
            ($sr->getCouponCode() ? '<br/> Coupon Code: '.$sr->getCouponCode().'' : '').
            '</td>'.
            '</tr></table>';
            $xmlStr .= <<< EOT
<item>
    <title><![CDATA[{$sr->getName()}]]></title>
    <description><![CDATA[{$description}]]></description>
</item>
EOT;

        }
        $xmlStr .= "
</channel>
</rss>";
        return $xmlStr;
    }


    protected function taggedProductHtml()
    {
         //store id is store view id
         $storeId =   (int) $this->getRequest()->getParam('sid');
         if ($storeId == null) {
            $storeId = Mage::app()->getStore()->getId();
         }
         $tagName = $this->getRequest()->getParam('tagName');

         $tagModel = Mage::getModel('tag/tag');
         $tagModel->loadByName($tagName);

         if ($tagModel->getId() && $tagModel->getStatus()==$tagModel->getApprovedStatus()) {
            $newurl = Mage::getUrl('rss/catalog/new');
            $title = Mage::helper('rss')->__('Products tagged with %s', $tagModel->getName());

            $xmlStr = <<< EOT
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
<channel>
    <link>{$newurl}</link>
    <title>{$title}</title>
    <description>{$title}</description>
EOT;
            $_collection = $tagModel->getEntityCollection()
                ->addTagFilter($tagModel->getId())
                ->addStoreFilter($storeId);


            $product = Mage::getModel('catalog/product');

	        Mage::getSingleton('core/resource_iterator')
                    ->walk($_collection->getSelect(), array(array($this, 'addTaggedItemXml')), array('xml'=>&$xmlStr, 'product'=>$product));

            $xmlStr .= "
</channel>
</rss>";
            return $xmlStr;
         }
    }

    public function addTaggedItemXml($args)
    {
        $product = $args['product'];
        $product->unsetData()->load($args['row']['entity_id']);
        $description = '<table><tr>'.
        '<td><a href="'.$product->getProductUrl().'"><img src="'.$product->getThumbnailUrl().'" border="0" align="left" height="75" width="75"></a></td>'.
        '<td  style="text-decoration:none;">'.$product->getDescription().
        '<p> Price:'.Mage::helper('core')->currency($product->getFinalPrice()).'</p>'.
        '</td>'.
        '</tr></table>';

        $args['xml'] .= <<< EOT
<item>
    <title><![CDATA[{$product->getName()}]]></title>
    <description><![CDATA[{$description}]]></description>
    <link><![CDATA[{$product->getProductUrl()}]]></link>
</item>
EOT;
    }




}
