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
 * @package     Mage_Rss
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Review form block
 *
 * @category   Mage
 * @package    Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_Block_Home extends Mage_XmlConnect_Block_Abstract
{

    protected function _toHtml()
    {
        $categoryModel = Mage::getResourceModel('xmlconnect/category_collection');

        /* TODO: Hardcoded banner */
        $additionalAttributes['home_banner'] = 'http://kd.varien.com/dev/yuriy.sorokolat/current/media/catalog/category/banner_home.png';
        $xml = $categoryModel->addNameToResult()
            ->addImageToResult()
            ->addIsActiveFilter()
            ->addLevelExactFilter(2)
            ->addLimit(0,6)
            ->load()
            ->toXml($additionalAttributes);
        return $xml;
    }

}
