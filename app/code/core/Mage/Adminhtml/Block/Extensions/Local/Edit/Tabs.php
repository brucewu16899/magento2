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
 * admin customer left menu
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Mage_Adminhtml_Block_Extensions_Local_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('extensions_local_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Installed Extension Info'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('package', array(
            'label'     => __('Package Info'),
            'content'   => $this->_getTabHtml('package'),
            'active'    => true,
        ));
/*
        $this->addTab('release', array(
            'label'     => __('Release Info'),
            'content'   => $this->_getTabHtml('release'),
        ));

        $this->addTab('maintainers', array(
            'label'     => __('Maintainers'),
            'content'   => $this->_getTabHtml('maintainers'),
        ));

        $this->addTab('depends', array(
            'label'     => __('Dependencies'),
            'content'   => $this->_getTabHtml('depends'),
        ));

        $this->addTab('contents', array(
            'label'     => __('Contents'),
            'content'   => $this->_getTabHtml('contents'),
        ));
*/
        return parent::_beforeToHtml();
    }

    protected function _getTabHtml($tab)
    {
        return $this->getLayout()
            ->createBlock('adminhtml/extensions_local_edit_tab_'.$tab)
            ->initForm()
            ->toHtml();
    }
}
