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
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml products tagged by tag
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Michael Bessolov <michael@varien.com>
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Tag_Product extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    public function __construct()
    {
        parent::__construct();

        switch( $this->getRequest()->getParam('ret') ) {
            case 'all':
                $url = Mage::helper('adminhtml')->getUrl('*/*/');
                break;

            case 'pending':
                $url = Mage::helper('adminhtml')->getUrl('*/*/pending');
                break;

            default:
                $url = Mage::helper('adminhtml')->getUrl('*/*/');
        }


        $this->_block = 'tag_product';
        $this->_controller = 'tag_product';
        $this->_removeButton('add');
        $this->setBackUrl($url);
        $this->_addBackButton();

        $tagInfo = Mage::getModel('tag/tag')
            ->load(Mage::registry('tagId'));

        $this->_headerText = Mage::helper('tag')->__("Products Tagged with '%s'", $tagInfo->getName());
    }

}
