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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Catalog product option model
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Option extends Mage_Core_Model_Abstract
{
    const OPTION_GROUP_TEXT   = 'text';
    const OPTION_GROUP_FILE   = 'file';
    const OPTION_GROUP_SELECT = 'select';
    const OPTION_GROUP_DATE   = 'date';

    const OPTION_TYPE_FIELD     = 'field';
    const OPTION_TYPE_AREA      = 'area';
    const OPTION_TYPE_FILE      = 'file';
    const OPTION_TYPE_DROP_DOWN = 'drop_down';
    const OPTION_TYPE_RADIO     = 'radio';
    const OPTION_TYPE_CHECKBOX  = 'checkbox';
    const OPTION_TYPE_MULTIPLE  = 'multiple';
    const OPTION_TYPE_DATE      = 'date';
    const OPTION_TYPE_DATE_TIME = 'date_time';
    const OPTION_TYPE_TIME      = 'time';

    protected $_options = array();

    protected $_valueInstance;

    protected $_product;

    public function __construct()
    {
        $this->_init('catalog/product_option');
    }

    /**
     * Enter description here...
     *
     * @return Mage_Catalog_Model_Product_Option_Value
     */
    public function getValueInstance()
    {
        if (!$this->_valueInstance) {
            $this->_valueInstance = Mage::getSingleton('catalog/product_option_value');
        }
        return $this->_valueInstance;
    }

    public function addOption($option)
    {
        $this->_options[] = $option;
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    public function setOptions($options)
    {
        $this->_options = $options;
        return $this;
    }

    public function unsetOptions()
    {
        $this->_options = array();
        return $this;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return $this->_product;
    }

    public function setProduct(Mage_Catalog_Model_Product $product)
    {
        $this->_product = $product;
        return $this;
    }

    public function getGroupByType($type)
    {
        $optionGroupsToTypes = array(
            self::OPTION_TYPE_FIELD => self::OPTION_GROUP_TEXT,
            self::OPTION_TYPE_AREA => self::OPTION_GROUP_TEXT,
            self::OPTION_TYPE_FILE => self::OPTION_GROUP_FILE,
            self::OPTION_TYPE_DROP_DOWN => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_RADIO => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_CHECKBOX => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_MULTIPLE => self::OPTION_GROUP_SELECT,
            self::OPTION_TYPE_DATE => self::OPTION_GROUP_DATE,
            self::OPTION_TYPE_DATE_TIME => self::OPTION_GROUP_DATE,
            self::OPTION_TYPE_TIME => self::OPTION_GROUP_DATE,
        );

        return isset($optionGroupsToTypes[$type])?$optionGroupsToTypes[$type]:'';
    }

    /**
     * Enter description here...
     *
     * @param array $option
     * @return unknown
     */
    public function saveOptions()
    {
//        Zend_Debug::dump($this->getOptions(), 'Options');die();
        foreach ($this->getOptions() as $option) {
            $this->setData($option)
                ->setData('product_id', $this->getProduct()->getId())
                ->setData('store_id', $this->getProduct()->getStoreId());

//            if ($this->getData('title') == 'Date') {
//                Zend_Debug::dump($this->getId(), 'ID');
//                Zend_Debug::dump($this->getData(), 'option');
//                die();
//            }

            if ($this->getData('option_id') == '0') {
                $this->unsetData('option_id');
            } else {
                $this->setId($this->getData('option_id'));
            }
            $isEdit = (bool)$this->getId()? true:false;

            if ($this->getData('is_delete') == '1') {// better strlen()
                if ($isEdit) {
                    $this->getValueInstance()->deleteValue($this->getId());
                    $this->deletePrices($this->getId());
                    $this->deleteTitles($this->getId());
                    $this->delete();
                }
            } else {
                if ($this->getData('previous_type') != '') {//better strlen()
                    $previousType = $this->getData('previous_type');
                    //if previous option has dfferent group from one is came now need to remove all data of previous group
                    if ($this->getGroupByType($previousType) != $this->getGroupByType($this->getData('type'))) {

                        switch ($this->getGroupByType($previousType)) {
                            case self::OPTION_GROUP_SELECT:
                                $this->unsetData('values');
                                if ($isEdit) {
                                    $this->getValueInstance()->deleteValue($this->getId());
                                }
                                break;
                            case self::OPTION_GROUP_FILE:
                                $this->setData('file_extension', '');
                                break;
                            case self::OPTION_GROUP_TEXT:
                                $this->setData('max_characters', '0');
                                break;
                            case self::OPTION_GROUP_DATE:
                                break;
                        }
//                        Zend_Debug::dump($this->getData());die();
                        if ($this->getGroupByType($this->getData('type')) == self::OPTION_GROUP_SELECT) {
                            $this->setData('sku', '');
                            $this->unsetData('price');
                            $this->unsetData('price_type');
                            if ($isEdit) {
                                $this->deletePrices($this->getId());
                            }
                        }

//                        if ($this->getGroupByType($previousType) == self::OPTION_GROUP_SELECT) {
//                            $this->unsetData('values');
//                            if ($isEdit) {
//                                $this->getValueInstance()->deleteValue($this->getId());
//                            }
//                        } else {
//                            if ($previousType == self::OPTION_TYPE_FIELD || $previousType == self::OPTION_TYPE_AREA) {
//                                $this->setData('max_characters', '0');
//                            } elseif ($previousType == self::OPTION_TYPE_FILE) {
//                                $this->setData('file_extension', '');
//                            }
//                            $this->setData('sku', '');
//                            $this->unsetData('price');
//                            $this->unsetData('price_type');
//                            if ($isEdit) {
//                                $this->deletePrices($this->getId());
//                            }
//                        }
                    }
                }
                $this->save();
//                if ($this->getData('title') == 'test') {
//                    Zend_Debug::dump($this->getId(), 'ID');
//                    Zend_Debug::dump($this->getData(), 'option');
//                    die();
//                }
            }
        }//eof foreach()

        return $this;
    }

    protected function _afterSave()
    {
        $this->getValueInstance()->unsetValues();
        if (is_array($this->getData('values'))) {
            foreach ($this->getData('values') as $value) {
                $this->getValueInstance()->addValue($value);
            }

            $this->getValueInstance()->setOption($this)
                ->saveValues();
        }

        return parent::_afterSave();
    }

    public function deletePrices($option_id)
    {
        $this->getResource()->deletePrices($option_id);
        return $this;
    }

    public function deleteTitles($option_id)
    {
        $this->getResource()->deleteTitles($option_id);
        return $this;
    }

    public function getProductOptionCollection(Mage_Catalog_Model_Product $product)
    {
        $collection = Mage::getResourceModel('catalog/product_option_collection')
            ->addFieldToFilter('product_id', $product->getId())
            ->getOptions($product->getStoreId());

        return $collection;
    }

    /**
     * Enter description here...
     *
     * @return Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Option_Value_Collection
     */
    public function getValuesCollection()
    {
        $collection = $this->getValueInstance()
            ->getValuesCollection($this);

        return $collection;
    }

    public function getOptionValuesByOptionId($optionIds, $store_id)
    {
        $collection = Mage::getModel('catalog/product_option_value')
            ->getValuesByOption($optionIds, $this->getId(), $store_id);

        return $collection;
    }

}