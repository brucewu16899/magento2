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
 * @category    Social
 * @package     Social_Facebook
 * @copyright   Copyright (c) 2009 Phoenix Medien GmbH & Co. KG (http://www.phoenix-medien.de)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Social_Facebook_Block_Head extends Mage_Core_Block_Template
{
    /**
     * Block Initialization
     *
     * @return Social_Facebook_Block_Head
     */
    protected function _construct()
    {
        $helper = Mage::helper('Social_Facebook_Helper_Data');
        if (!$helper->isEnabled()) {
            return;
        }
        parent::_construct();

        /** @var $product Mage_Catalog_Model_Product */
        $product = Mage::registry('product');

        if ($product) {
            $this->setTemplate('page.phtml');

            $tags[] = array(
                'property'  => 'fb:app_id',
                'content'   => $helper->getAppId()
            );
            $tags[] = array(
                'property'  => 'og:type',
                'content'   => $helper->getAppName() . ':' . $helper->getObjectType()
            );
            $tags[] = array(
                'property'  => 'og:url',
                'content'   => Mage::getUrl('facebook/index/page', array('id' => $product->getId()))
            );
            $tags[] = array(
                'property'  => 'og:title',
                'content'   => $this->escapeHtml($product->getName())
            );
            $tags[] = array(
                'property'  => 'og:image',
                'content'   => $this->escapeHtml(Mage::helper('Mage_Catalog_Helper_Image')->init($product, 'image')->resize(256))
            );
            $tags[] = array(
                'property'  => 'og:description',
                'content'   => $this->escapeHtml($product->getShortDescription())
            );
            $tags[] = array(
                'property'  => $helper->getAppName(). ':price',
                'content'   => Mage::helper('Mage_Core_Helper_Data')->currency($product->getFinalPrice(), true, false)
            );

            $this->setMetaTags($tags);

            $this->setRedirectUrl($product->getUrlModel()->getUrlInStore($product));

            $this->setAppName($helper->getAppName());
        }

        return $this;
    }
}