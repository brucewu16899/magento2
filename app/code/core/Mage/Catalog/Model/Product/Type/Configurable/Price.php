<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product type price model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Product_Type_Configurable_Price extends Mage_Catalog_Model_Product_Type_Price
{
    /**
     * Get product final price
     *
     * @param   double $qty
     * @param   Mage_Catalog_Model_Product $product
     * @return  double
     */
    public function getFinalPrice($qty=null, $product)
    {
        if (is_null($qty) && !is_null($product->getCalculatedFinalPrice())) {
            return $product->getCalculatedFinalPrice();
        }

        $finalPrice = $this->getBasePrice($product, $qty);
        $product->setFinalPrice($finalPrice);
        Mage::dispatchEvent('catalog_product_get_final_price', array('product' => $product, 'qty' => $qty));
        $finalPrice = $product->getData('final_price');

        $finalPrice += $this->getTotalConfigurableItemsPrice($product, $qty);
        $finalPrice = $this->_applyOptionsPrice($product, $qty, $finalPrice);
        $finalPrice = max(0, $finalPrice);

        $product->setFinalPrice($finalPrice);
        return $finalPrice;
    }

    /**
     * Get Total price for configurable items
     *
     * @param Mage_Catalog_Model_Product $product
     * @param null|float $qty
     * @return float
     */
    public function getTotalConfigurableItemsPrice($product, $qty = null)
    {
        $price = 0.0;

        $product->getTypeInstance()
                ->setStoreFilter($product->getStore(), $product);
        $attributes = $product->getTypeInstance()
                ->getConfigurableAttributes($product);

        $selectedAttributes = array();
        if ($product->getCustomOption('attributes')) {
            $selectedAttributes = unserialize($product->getCustomOption('attributes')->getValue());
        }

        $basePrice = $this->getBasePrice($product, $qty);
        foreach ($attributes as $attribute) {
            $attributeId = $attribute->getProductAttribute()->getId();
            $value = $this->_getValueByIndex(
                $attribute->getPrices() ? $attribute->getPrices() : array(),
                isset($selectedAttributes[$attributeId]) ? $selectedAttributes[$attributeId] : null
            );
            $product->setParentId(true);
            if ($value) {
                if ($value['pricing_value'] != 0) {
                    $product->setConfigurablePrice($this->_calcSelectionPrice($value, $basePrice));
                    Mage::dispatchEvent(
                        'catalog_product_type_configurable_price',
                        array('product' => $product)
                    );
                    $price += $product->getConfigurablePrice();
                }
            }
        }
        return $price;
    }


    /**
     * Calculate configurable product selection price
     *
     * @param   array $priceInfo
     * @param   decimal $productPrice
     * @return  decimal
     */
    protected function _calcSelectionPrice($priceInfo, $productPrice)
    {
        if($priceInfo['is_percent']) {
            $ratio = $priceInfo['pricing_value']/100;
            $price = $productPrice * $ratio;
        } else {
            $price = $priceInfo['pricing_value'];
        }
        return $price;
    }

    protected function _getValueByIndex($values, $index) {
        foreach ($values as $value) {
            if($value['value_index'] == $index) {
                return $value;
            }
        }
        return false;
    }
}
