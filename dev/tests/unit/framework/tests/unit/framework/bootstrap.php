<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$rootDir = realpath(__DIR__ . '/../../../../../../../');

$codeDirs = array(
    $rootDir . '/lib/',
    $rootDir . '/dev/tests/unit/framework/',
    $rootDir . '/app/code/core/',
);

set_include_path(implode(PATH_SEPARATOR, $codeDirs) . PATH_SEPARATOR . get_include_path());
spl_autoload_register(function ($class) {
    $file = str_replace('_', '/', $class) . '.php';
    require_once $file;
});
