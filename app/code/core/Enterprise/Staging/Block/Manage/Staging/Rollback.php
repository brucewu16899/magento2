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
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Staging rollback setting block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Rollback extends Mage_Adminhtml_Block_Widget
{
	private $_rollbackSettingsBlock = array();
    private $_rollbackSettingsBlockDefaultTemplate = 'enterprise/staging/rollback/settings.phtml';
    private $_rollbackSettingsBlockTypes = array();

    public function __construct()
    {
        parent::__construct();
        //$this->setTemplate('enterprise/staging/manage/staging/rollback.phtml');
        //$this->setId('enterprise_staging_rollback');
    }

    /**
     * Retrieve currently edited staging object
     *
     * @return Enterprise_Staging_Block_Manage_Staging
     */
    public function getStaging()
    {
        if (!($this->getData('staging') instanceof Enterprise_Staging_Model_Staging)) {
            $this->setData('staging', Mage::registry('staging'));
        }
        return $this->getData('staging');
    }

    protected function _getRollbackSettingsBlock($stagingType)
    {
        if (!isset($this->_rollbackSettingsBlock[$stagingType])) {
            $block = 'enterprise_staging/staging_rollback_settings';
            if (isset($this->_rollbackSettingsBlockTypes[$stagingType])) {
                if ($this->_rollbackSettingsBlockTypes[$stagingType]['block'] != '') {
                    $block = $this->_rollbackSettingsBlockTypes[$stagingType]['block'];
                }
            }
            $this->_rollbackSettingsBlock[$stagingType] = $this->getLayout()->createBlock($block);
        }
        return $this->_rollbackSettingsBlock[$stagingType];
    }

    protected function _getRollbackSettingsBlockTemplate($stagingType)
    {
        if (isset($this->_rollbackSettingsBlockTypes[$stagingType])) {
            if ($this->_rollbackSettingsBlockTypes[$stagingType]['template'] != '') {
                return $this->_rollbackSettingsBlockTypes[$stagingType]['template'];
            }
        }
        return $this->_rollbackSettingsBlockTypes;
    }

    /**
     * Returns staging rollback settings block html
     *
     * @param Mage_Catalog_Model_Product $staging
     * @param boolean $displayMinimalPrice
     */
    public function getRollbackSettingsHtml($staging = null, $idSuffix='')
    {
    	if (is_null($staging)) {
    		$staging = $this->getStaging();
    	}
        return $this->_getRollbackSettingsBlock($staging->getType())
            ->setTemplate($this->_getRollbackSettingsBlockTemplate($staging->getType()))
            ->setStaging($staging)
            ->setIdSuffix($idSuffix)
            ->toHtml();
    }

    protected function _toHtml()
    {
    	return $this->getRollbackSettingsHtml();
    }

    /**
     * Adding customized rollback settings block for staging type
     *
     * @param string $type
     * @param string $block
     * @param string $template
     */
    public function addRollbackSettingsBlockType($type, $block = '', $template = '')
    {
        if ($type) {
            $this->_rollbackSettingsBlockTypes[$type] = array(
                'block'     => $block,
                'template'  => $template
            );
        }
    }
}