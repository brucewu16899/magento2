<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review form block
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Block_Catalog_New extends Mage_Rss_Block_Catalog_Abstract
{
    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        //$this->setCacheKey('rss_catalog_new_'.$this->_getStoreId());
        //$this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
        $storeId = $this->_getStoreId();

        $newurl = Mage::getUrl('rss/catalog/new/store_id/' . $storeId);
        $title = Mage::helper('Mage_Rss_Helper_Data')->__('New Products from %s',Mage::app()->getStore()->getGroup()->getName());
        $lang = Mage::getStoreConfig('general/locale/code');

        $rssObj = Mage::getModel('Mage_Rss_Model_Rss');
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                'language'    => $lang
                );
        $rssObj->_addHeader($data);
/*
oringinal price - getPrice() - inputed in admin
special price - getSpecialPrice()
getFinalPrice() - used in shopping cart calculations
*/

        $product = Mage::getModel('Mage_Catalog_Model_Product');
        $todayDate = $product->getResource()->formatDate(time());

        $products = $product->getCollection()
            ->setStoreId($storeId)
            ->addStoreFilter()

            ->addAttributeToFilter('news_from_date', array('date'=>true, 'to'=> $todayDate))
            ->addAttributeToFilter(
                array(
                     array('attribute'=>'news_to_date', 'date'=>true, 'from'=>$todayDate),
                     array('attribute'=>'news_to_date', 'is' => new Zend_Db_Expr('null'))
                ),
                '',
                'left'
            )
            ->addAttributeToSort('news_from_date','desc')
            ->addAttributeToSelect(array('name', 'short_description', 'description', 'thumbnail'), 'inner')
            ->addAttributeToSelect(
                array(
                    'price', 'special_price', 'special_from_date', 'special_to_date',
                    'msrp_enabled', 'msrp_display_actual_price_type', 'msrp'
                ),
                'left'
            )
            ->applyFrontendPriceLimitations()
        ;

        $products->setVisibility(Mage::getSingleton('Mage_Catalog_Model_Product_Visibility')->getVisibleInCatalogIds());

        /*
        using resource iterator to load the data one by one
        instead of loading all at the same time. loading all data at the same time can cause the big memory allocation.
        */

        Mage::getSingleton('Mage_Core_Model_Resource_Iterator')->walk(
                $products->getSelect(),
                array(array($this, 'addNewItemXmlCallback')),
                array('rssObj'=> $rssObj, 'product'=>$product)
        );

        return $rssObj->createRssXml();
    }

    /**
     * Preparing data and adding to rss object
     *
     * @param array $args
     */
    public function addNewItemXmlCallback($args)
    {
        $product = $args['product'];

        $product->setAllowedInRss(true);
        $product->setAllowedPriceInRss(true);
        Mage::dispatchEvent('rss_catalog_new_xml_callback', $args);

        if (!$product->getAllowedInRss()) {
            //Skip adding product to RSS
            return;
        }

        $allowedPriceInRss = $product->getAllowedPriceInRss();

        //$product->unsetData()->load($args['row']['entity_id']);
        $product->setData($args['row']);
        $description = '<table><tr>'
            . '<td><a href="'.$product->getProductUrl().'"><img src="'
            . $this->helper('Mage_Catalog_Helper_Image')->init($product, 'thumbnail')->resize(75, 75)
            .'" border="0" align="left" height="75" width="75"></a></td>'.
            '<td  style="text-decoration:none;">'.$product->getDescription();

        if ($allowedPriceInRss) {
            $description .= $this->getPriceHtml($product,true);
        }

        $description .= '</td>'.
            '</tr></table>';

        $rssObj = $args['rssObj'];
        $data = array(
                'title'         => $product->getName(),
                'link'          => $product->getProductUrl(),
                'description'   => $description,
            );
        $rssObj->_addEntry($data);
    }
}
