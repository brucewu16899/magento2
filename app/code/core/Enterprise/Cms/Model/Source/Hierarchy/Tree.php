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
 * Cms Hierarchy Tree Source Model
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Model_Source_Hierarchy_Tree
{
    /**
     * Retrieve options array for hierarchy tree
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        /* @var $hierarchyCollection Enterprise_Cms_Model_Mysql4_Hierarchy_Collection */
        $hierarchyCollection = Mage::getModel('enterprise_cms/hierarchy')->getCollection();

        foreach ($hierarchyCollection as $hierarchy) {
            /* @var $hierarchy Enterprise_Cms_Model_Hierarchy */
            $options[] = array(
                'label' => $hierarchy->getRootNode()->getLabel(),
                'value' => $hierarchy->getRootNode()->getId()
            );

            /* @var $nodeCollection Enterprise_Cms_Model_Mysql4_Hierarchy_Node_Collection */
            $nodeCollection = Mage::getModel('enterprise_cms/hierarchy_node')->getCollection();
            $nodeCollection->addTreeFilter($hierarchy->getId())
                ->joinCmsPage()
                ->setTreeOrder();
            foreach ($nodeCollection as $node) {
                /* @var $node Enterprise_Cms_Model_Hierarchy_Node */
                $options[] = array(
                    'label' => str_repeat('&nbsp;&nbsp;', $node->getLevel()) . $node->getLabel(),
                    'value' => $node->getId()
                );
            }
        }

        return $options;
    }
}
