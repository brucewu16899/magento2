<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Dataflow Batch import resource model
 *
 * @category    Mage
 * @package     Mage_Dataflow
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Dataflow_Model_Resource_Batch_Import extends Mage_Dataflow_Model_Resource_Batch_Abstract
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('dataflow_batch_import', 'batch_import_id');
    }
}
