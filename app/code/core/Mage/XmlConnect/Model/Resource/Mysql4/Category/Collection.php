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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Category resource collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Resource_Mysql4_Category_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection
{
    /**
     * Level of parent categories
     */
    const PARENT_CATEGORIES_LEVEL = 2;

    /**
     * Image resize parameters
     */
    const IMAGE_RESIZE_PARAM = 80;

    /**
     * To add parent_id node to result flag
     */
    protected $_showParentId = false;

    /**
     * Adding image attribute to result
     *
     * @return Mage_XmlConnect_Model_Resource_Mysql4_Category_Collection
     */
    public function addImageToResult()
    {
        $this->addAttributeToSelect('image');
        return $this;
    }

    protected function _beforeLoad()
    {
        $this->addNameToResult();
        $this->addIsActiveFilter();
        return parent::_beforeLoad();
    }

    /**
     * @param array $additionalAtrributes Additional nodes for xml 
     * @return string
     */
    public function toXml(array $additionalAtrributes = array())
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
        <category>
           <items>
           ';

        foreach ($this as $item)
        {
            $attributes = array('label', 'background', 'entity_id', 'content_type', 'icon');
            if (strlen($item->image) < 1)
            {
                $item->image = 'no_selection';
            }
            $item->icon = Mage::helper('catalog/category_image')->init($item, 'image')->resize(self::IMAGE_RESIZE_PARAM);


            if ($this->_showParentId)
            {
                $attributes[] = 'parent_id';
            }
            $item->label = $item->name;
            $item->content_type = $item->hasChildren() ? 'categories' : 'products' ;

            /* Hardcode */
            $item->background = 'http://kd.varien.com/dev/yuriy.sorokolat/current/media/catalog/category/background_img.png';

            $xml .= $item->toXml($attributes, 'item', false, false);
        }
        $xml .= '</items>
                ';

        $xmlModel = new Varien_Simplexml_Element('<node></node>');
        foreach ($additionalAtrributes as $attrKey => $value)
        {
            $xml .= "<{$attrKey}>{$xmlModel->xmlentities($value)}</{$attrKey}>";
        }

        $xml .= '</category>';

        return $xml;
    }

    public function addLevelExactFilter($level)
    {
        $this->getSelect()->where('e.level = ?', $level);
        return $this;
    }

    public function addLimit($offset, $count)
    {
        $this->getSelect()->limit($count, $offset);
        return $this;
    }

    public function addParentIdFilter($parentId)
    {
        if (!is_null($parentId))
        {
            $this->_showParentId = true;
            $this->getSelect()->where('e.parent_id = ?', (int)$parentId);
        }
        else
        {
            $this->addLevelExactFilter(self::PARENT_CATEGORIES_LEVEL);
        }
        return $this;
    }

}