<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @package    Enterprise_Cms
 * @copyright  Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Cms Pages Hierarchy Tree Model
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Model_Hierarchy extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('enterprise_cms/hierarchy');
    }

    /**
     * Retrieve Resource instance wrapper
     *
     * @return Enterprise_Cms_Model_Mysql4_Hierarchy
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Set Hierarchy root node
     *
     * @param Enterprise_Cms_Model_Hierarchy_Node $node
     * @return Enterprise_Cms_Model_Hierarchy
     */
    public function setRootNode(Enterprise_Cms_Model_Hierarchy_Node $node)
    {
        return $this->setData('root_node', $node);
    }

    /**
     * Retrieve Hierarchy root node
     *
     * @return Enterprise_Cms_Model_Hierarchy_Node
     */
    public function getRootNode()
    {
        if (!$this->hasData('root_node')) {
            $node = Mage::getModel('enterprise_cms/hierarchy_node');
            $node->loadByHierarchy($this->getId());
            $this->setData('root_node', $node);
        }
        return $this->_getData('root_node');
    }
}
