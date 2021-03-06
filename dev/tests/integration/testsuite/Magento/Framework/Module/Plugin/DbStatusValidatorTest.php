<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Framework\Module\Plugin;

class DbStatusValidatorTest extends \Magento\TestFramework\TestCase\AbstractController
{
    public function testValidationUpToDateDb()
    {
        $this->dispatch('index/index');
    }

    public function testValidationOutdatedDb()
    {
        $resourceName = 'adminnotification_setup';
        /*reset versions*/
        /** @var \Magento\Framework\Module\ResourceInterface $resource */
        $resource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Framework\Module\ResourceInterface'
        );
        $dbVersion = $resource->getDbVersion($resourceName);
        $dbDataVersion = $resource->getDataVersion($resourceName);
        try {
            $resource->setDbVersion($resourceName, '0.1');
            $resource->setDataVersion($resourceName, '0.1');
            /** @var \Magento\Framework\Cache\FrontendInterface $cache */
            $cache = $this->_objectManager->get('Magento\Framework\App\Cache\Type\Config');
            $cache->clean();

            try {
                /* This triggers plugin to be executed */
                $this->dispatch('index/index');
            } catch (\Magento\Framework\Module\Exception $e) {
                if ($e->getMessage() != 'Looks like database is outdated. Please, use setup tool to perform update') {
                    $failureMessage = "DB status validation doesn't work properly. Caught exception message is '"
                        . $e->getMessage() . "'";
                }
            }
        } catch (\Exception $e) {
            $failureMessage = "Impossible to continue other tests, because database is broken: {$e}";
        }

        $resource->setDbVersion($resourceName, $dbVersion);
        $resource->setDataVersion($resourceName, $dbDataVersion);

        if (isset($failureMessage)) {
            $this->fail($failureMessage);
        }
    }
}
