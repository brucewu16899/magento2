<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  static_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$baseDir = realpath(__DIR__ . '/../../../../../');

return array(
    'report_dir' => "{$baseDir}/dev/tests/static/report",
    'white_list' => array(
        "{$baseDir}/dev/tests/static"
    ),
    'black_list' => array(
        /* Files that intentionally violate the requirements for testing purposes */
        "{$baseDir}/dev/tests/static/testsuite/Php/Exemplar/_files/phpcs/input",
        "{$baseDir}/dev/tests/static/testsuite/Php/Exemplar/_files/phpmd/input"
    )
);
