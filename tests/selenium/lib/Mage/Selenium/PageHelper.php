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
 * @category    tests
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Page helper object
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_PageHelper extends Mage_Selenium_AbstractHelper
{
    /**
     * Last validation result
     *
     * @var boolean
     */
    protected $_validationFailed = false;

    /**
     * SUT helper instance
     *
     * @var Mage_Selenium_SutHelper
     */
    protected $_sutHelper = null;

    /**
     * Validates current page properties
     *
     * @return Mage_Selenium_PageHelper
     * @throws @TODO
     */
    public function validateCurrentPage()
    {
        $this->_validationFailed = false;
        // @TODO check for no fatal errors, notices, warning
        // @TODO check title
        return $this;
    }

    /**
     * Returns true if the last page validation failed
     *
     * @return boolean
     */
    public function validationFailed()
    {
        return $this->_validationFailed;
    }

    /**
     * Set SUT helper instance to access application info
     *
     * @param Mage_Selenium_SutHelper $sutHelper
     * @return Mage_Selenium_AbstractHelper
     */
    public function setSutHelper(Mage_Selenium_SutHelper $sutHelper)
    {
        $this->_sutHelper = $sutHelper;
        return $this;
    }

    /**
     * Return URL of a specified page
     *
     * @param string $page Page in MCA format
     * @return string
     */
    public function getPageUrl($page)
    {
        $pageData = $this->_config->getUimapValue($this->_sutHelper->getArea(), $page);
        if ((false === $pageData) || (!isset($pageData['mca'])) || empty($pageData['mca'])) {
            var_dump($page);
            die('Page mca is not defined');
        }
        $url = $this->_sutHelper->getBaseUrl() . $pageData['mca'];
        return $url;
    }

}
