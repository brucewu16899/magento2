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
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect index controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_XmlConnect_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Declare content type header
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
    }

    /**
     * Default action
     *
     */
    public function indexAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Category list
     *
     */
    public function categoryAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Filter product list
     *
     */
    public function filtersAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Product information
     *
     */
    public function productAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Product options list
     *
     */
    public function optionsAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }


    /**
     * Product gallery images list
     *
     */
    public function galleryAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Product reviews list
     *
     */
    public function reviewsAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }

    /**
     * Add new review
     *
     */
    public function reviewAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }
}