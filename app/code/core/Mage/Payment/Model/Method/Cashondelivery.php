<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cash on delivery payment method model
 */
class Mage_Payment_Model_Method_Cashondelivery extends Mage_Payment_Model_Method_Abstract
{

    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code  = 'cashondelivery';

    /**
     * Cash On Delivery payment block paths
     *
     * @var string
     */
    protected $_formBlockType = 'Mage_Payment_Block_Form_Cashondelivery';
    protected $_infoBlockType = 'Mage_Payment_Block_Info';

    /**
     * Get instructions text from config
     *
     * @return string
     */
    public function getInstructions()
    {
        return trim($this->getConfigData('instructions'));
    }

}