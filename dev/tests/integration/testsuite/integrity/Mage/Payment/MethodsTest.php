<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Payment
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Locate all payment methods in the system and verify declaration of their blocks
 *
 * @group integrity
 */
class Integrity_Mage_Payment_MethodsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param string $methodClass
     * @param string $code
     * dataProvider paymentMethodDataProvider
     * @throws Exception on various assertion failures
     */
    public function testPaymentMethod(/*$code, $methodClass*/)
    {
        $this->markTestIncomplete('Need to fix DI dependencies');

        $storeId = Mage::app()->getStore()->getId();
        /** @var $model Mage_Payment_Model_Method_Abstract */
        if (empty($methodClass)) {
            /**
             * Note that $code is not whatever the payment method getCode() returns
             */
            $this->fail("Model of '{$code}' payment method is not found."); // prevent fatal error
        }
        $model = new $methodClass;
        $this->assertNotEmpty($model->getTitle());
        foreach (array($model->getFormBlockType(), $model->getInfoBlockType()) as $blockClass) {
            $message = "Block class: {$blockClass}";
            $block = new $blockClass;
            $block->setArea('frontend');
            $this->assertFileExists($block->getTemplateFile(), $message);
            if ($model->canUseInternal()) {
                try {
                    Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
                    $block->setArea('adminhtml');
                    $this->assertFileExists($block->getTemplateFile(), $message);
                    Mage::app()->getStore()->setId($storeId);
                } catch (Exception $e) {
                    Mage::app()->getStore()->setId($storeId);
                    throw $e;
                }
            }
        }
    }

    /**
     * @return array
     */
    public function paymentMethodDataProvider()
    {
        $helper = new Mage_Payment_Helper_Data;
        $result = array();
        foreach ($helper->getPaymentMethods() as $code => $method) {
            $result[] = array($code, $method['model']);
        }
        return $result;
    }
}
