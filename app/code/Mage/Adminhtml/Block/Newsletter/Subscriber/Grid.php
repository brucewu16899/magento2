<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml newsletter subscribers grid block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Newsletter_Subscriber_Grid extends Mage_Backend_Block_Widget_Grid
{
    /**
     * Prepare collection for grid
     *
     * @return Mage_Backend_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {

        if ($this->getRequest()->getParam('queue', false)) {
            $this->getCollection()->useQueue(Mage::getModel('Mage_Newsletter_Model_Queue')
                ->load($this->getRequest()->getParam('queue')));
        }

        return parent::_prepareCollection();
    }
}
