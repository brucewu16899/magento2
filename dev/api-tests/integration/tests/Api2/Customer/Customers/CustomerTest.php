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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Magento_Test
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Test for customers API2 by customer api user
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Api2_Customer_Customers_CustomerTest extends Magento_Test_Webservice_Rest_Customer
{
    /**
     * Test create customer
     */
    public function testCreate()
    {
        $response = $this->callPost('customers', array());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $response->getStatus());
    }

    /**
     * Test retrieve customers collection
     */
    public function testRetrieve()
    {
        $response = $this->callGet('customers');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $response->getStatus());
    }

    /**
     * Test update action
     */
    public function testUpdate()
    {
        $response = $this->callPut('customers', array());
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $response->getStatus());
    }

    /**
     * Test delete action
     */
    public function testDelete()
    {
        $response = $this->callDelete('customers');
        $this->assertEquals(Mage_Api2_Model_Server::HTTP_NOT_FOUND, $response->getStatus());
    }
}
