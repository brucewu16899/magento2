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
 * @subpackage  runner
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

define('SELENIUM_UNIT_TESTS_BASEDIR', realpath(dirname(__FILE__)));

define('SELENIUM_TESTS_BASEDIR', realpath(SELENIUM_UNIT_TESTS_BASEDIR . DIRECTORY_SEPARATOR . '..'
    . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..'));
define('SELENIUM_TESTS_FWDIR', realpath(SELENIUM_TESTS_BASEDIR . DIRECTORY_SEPARATOR . 'framework'));

set_include_path(implode(PATH_SEPARATOR, array(
    SELENIUM_UNIT_TESTS_BASEDIR,
    SELENIUM_TESTS_FWDIR,
    get_include_path(),
)));

require_once SELENIUM_TESTS_FWDIR . '/functions.php';
require_once 'Mage/Autoloader.php';
Mage_Autoloader::register();
