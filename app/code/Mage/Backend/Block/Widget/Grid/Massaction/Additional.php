<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend widget grid massaction additional action
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Mage_Backend_Block_Widget_Grid_Massaction_Additional extends Mage_Backend_Block_Widget_Form
{
    /**
     * @var Mage_Core_Model_Layout_Argument_HandlerFactory
     */
    protected $_handlerFactory;

    /**
     * @param Mage_Backend_Block_Template_Context $context
     * @param Mage_Core_Model_Layout_Argument_HandlerFactory $handlerFactory
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Block_Template_Context $context,
        Mage_Core_Model_Layout_Argument_HandlerFactory $handlerFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);

        $this->_handlerFactory = $handlerFactory;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return Mage_Backend_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        foreach ($this->getData('fields') as $itemId => $item) {
            $this->_prepareFormItem($item);
            $form->addField($itemId, $item['type'], $item);
        }
        $this->setForm($form);
        return $this;
    }

    /**
     * Prepare form item
     *
     * @param array $item
     */
    protected function _prepareFormItem(array &$item)
    {
        if ($item['type'] == 'select' && is_string($item['values'])) {
            $argumentHandler = $this->_handlerFactory->getArgumentHandlerByType('options');
            $item['values'] = $argumentHandler->process($item['values']);
        }
        $item['class'] = isset($item['class']) ? $item['class'] . ' absolute-advice' : 'absolute-advice';
    }
}