<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Staging merge settings of staging website type block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Staging_Merge_Settings_Website extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('staging/merge/settings/website.phtml');
        $this->setId('staging_website_mapper');
        $this->setUseAjax(true);
        $this->setRowInitCallback($this->getJsObjectName().'.stagingWebsiteMapperRowInit');

        $this->setIsReadyForMerge(true);
    }

    /**
     * prepare layout
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     */
    protected function _prepareLayout()
    {
        $this->setChild('items',
            $this->getLayout()
                ->createBlock('Enterprise_Staging_Block_Adminhtml_Staging_Edit_Tabs_Item')
                ->setFieldNameSuffix('map[staging_items]')
        );

        $this->setChild('schedule',
            $this->getLayout()
                ->createBlock('Enterprise_Staging_Block_Adminhtml_Staging_Edit_Tabs_Schedule')
                ->setFieldNameSuffix('map[schedule]')
                ->setStagingJsObjectName($this->getJsObjectName())
        );
        return parent::_prepareLayout();
    }

    /**
     * Retrieve currently edited staging object
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function getStaging()
    {
        if (!($this->getData('staging') instanceof Enterprise_Staging_Model_Staging)) {
            $this->setData('staging', Mage::registry('staging'));
        }
        return $this->getData('staging');
    }

    /**
     * Return save url
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/mergePost', array('_current'=>true));
    }

    /**
     * Retrieve website collection
     *
     * @return Varien_Object
     */
    public function getWebsiteCollection()
    {
        $collection = Mage::getModel('Mage_Core_Model_Website')
            ->getResourceCollection()
            ->addFieldToFilter('website_id',array('nin'=>array(0, $this->getStaging()->getStagingWebsiteId())));

        return $collection;
    }

    /**
     * Retrieve Staging Website Collection
     *
     * @return array
     */
    public function getStagingWebsiteCollection()
    {
        $staging = $this->getStaging();
        if ($staging) {
            $stagingWebsite = $this->getStaging()->getStagingWebsite();
            if ($stagingWebsite) {
                return array($stagingWebsite);
            }
        }
        return array();
    }

    /**
     * Retrieve stores collection
     *
     * @return Varien_Object
     */
    public function getAllStoresCollection()
    {
        return Mage::app()->getStores();
    }

    /**
     * Retrieve stores collection Json
     *
     * @return string (Json)
     */
    public function getAllStoresJson()
    {
        $stores = array();
        foreach ($this->getAllStoresCollection() as $store) {
            $stores[$store->getWebsiteId()][] = $store->getData();
        }
        if (!$stores) {
            return '{}';
        } else {
            return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($stores);
        }
    }

    /**
     * Retrieve Main buttons html
     */
    public function getMainButtonsHtml()
    {
        $html = '';
        //$html = parent::getMainButtonsHtml();
        if($this->getIsReadyForMerge()){
            $html.= $this->getChildHtml('merge_button');
        }
        return $html;
    }
}
