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
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

class Enterprise_Staging_Model_Staging_Adapter_Website extends Enterprise_Staging_Model_Staging_Adapter_Abstract
{
    /**
     *
     *
     * @return Enterprise_Staging_Model_Staging_Adapter_Website
     */
    public function create(Enterprise_Staging_Model_Staging $staging)
    {
        $mapper     = $staging->getMapperInstance();
        $websites   = $mapper->getWebsites();

        foreach ($websites as $website) {
            $masterWebsiteId = $website->getMasterWebsiteId();

            $stagingWebsite   = Mage::getModel('core/website');

            $stagingWebsite->setData('is_staging', 1);
            $stagingWebsite->setData('code', $website->getCode());
            $stagingWebsite->setData('name', $website->getName());

            $stagingWebsite->setData('base_url', $website->getBaseUrl());
            $stagingWebsite->setData('base_secure_url', $website->getBaseSecureUrl());

            $stagingWebsite->setData('master_login', $website->getMasterLogin());

            $password = trim($website->getMasterPassword());
            if ($password) {
                 if(Mage::helper('core/string')->strlen($password)<6){
                    Mage::throwException(Mage::helper('enterprise_staging')->__('Password must have at least 6 characters. Leading or trailing spaces will be ignored.'));
                }
            }
            $stagingWebsite->setData('master_password' , Mage::helper('core')->encrypt($password));

            if (!$stagingWebsite->getId()) {
                $value = Mage::getModel('core/date')->gmtDate();
                $stagingWebsite->setCreatedAt($value);
            } else {
                $value = Mage::getModel('core/date')->gmtDate();
                $stagingWebsite->setUpdatedAt($value);
            }

            $stagingWebsite->save();

            $entryPoint = Mage::getModel('enterprise_staging/entry')
                ->setWebsite($stagingWebsite)->save();

            $stagingWebsiteId = (int)$stagingWebsite->getId();

            $website->setStagingWebsite($stagingWebsite);
            $website->setStagingWebsiteId($stagingWebsiteId);

            $this->_saveSystemConfig($staging, $stagingWebsite, $entryPoint);

            $staging->updateAttribute('master_website_id', $masterWebsiteId);
            $staging->updateAttribute('staging_website_id', $stagingWebsiteId);

            Mage::dispatchEvent('staging_website_create_after', array(
                'old_website_id' => $masterWebsiteId, 'new_website_id' => $stagingWebsiteId)
            );

            break;
        }
        return $this;
    }

    /**
     * Save system config resource model
     *
     * @param Enterprise_Staging_Model_Staging  $staging
     * @param Mage_Core_Model_Website           $stagingWebsite
     * @return Enterprise_Staging_Model_Staging_Adapter_Website
     */
    protected function _saveSystemConfig($staging, Mage_Core_Model_Website $stagingWebsite, $entryPoint = null)
    {
        $masterWebsite = $staging->getMasterWebsite();

        $unsecureBaseUrl = $stagingWebsite->getBaseUrl();
        $secureBaseUrl   = $stagingWebsite->getBaseSecureUrl();
        if ($entryPoint && $entryPoint->isAutomatic()) {
            $unsecureBaseUrl = $entryPoint->getBaseUrl();
            $secureBaseUrl   = $entryPoint->getBaseUrl(true);
        }

        $unsecureBaseUrl = $this->_getIndexedUrl($unsecureBaseUrl);
        $secureBaseUrl = $this->_getIndexedUrl($secureBaseUrl);

        $unsecureConf = Mage::getConfig()->getNode('default/web/unsecure');
        $secureConf = Mage::getConfig()->getNode('default/web/secure');

        if (!$masterWebsite->getIsStaging()) {
            $originalBaseUrl = (string) $masterWebsite->getConfig("web/secure/base_url");
        } else {
            $originalBaseUrl = (string) Mage::getConfig()->getNode("default/web/unsecure/base_url");
        }

        $this->_saveUrlsInSystemConfig($stagingWebsite, $originalBaseUrl, $unsecureBaseUrl, 'unsecure' , $unsecureConf);

        if (!$masterWebsite->getIsStaging()) {
            $originalBaseUrl = (string) $masterWebsite->getConfig("web/secure/base_url");
        } else {
            $originalBaseUrl = (string) Mage::getConfig()->getNode("default/web/secure/base_url");
        }
        $this->_saveUrlsInSystemConfig($stagingWebsite, $originalBaseUrl, $secureBaseUrl, 'secure', $secureConf);

        return $this;
    }

    /**
     * Process core config data
     *
     * @param Mage_Core_Model_Website   $stagingWebsite
     * @param string $originalBaseUrl
     * @param string $baseUrl
     * @param string $mode
     * @param Varien_Simplexml_Element  $xmlConfig
     */
    protected function _saveUrlsInSystemConfig($stagingWebsite, $originalBaseUrl, $baseUrl, $mode = 'unsecure', $xmlConfig)
    {
        foreach ($xmlConfig->children() AS $nodeName => $nodeValue) {
            if ($mode == 'secure' || $mode == 'unsecure') {
                if ($nodeName == 'base_url' || $nodeName == 'base_web_url' || $nodeName == 'base_link_url') {
                    $nodeValue = $baseUrl;
                } elseif ($mode == 'unsecure') {
                    if (strpos($nodeValue, '{{unsecure_base_url}}') !== false) {
                        $nodeValue = str_replace('{{unsecure_base_url}}', $originalBaseUrl, $nodeValue);
                    }
                } elseif ($mode == 'secure') {
                    if (strpos($nodeValue, '{{secure_base_url}}') !== false) {
                        $nodeValue = str_replace('{{secure_base_url}}', $originalBaseUrl, $nodeValue);
                    }
                }
            }

            $config = Mage::getModel('core/config_data');
            $path = 'web/' . $mode . '/' . $nodeName;
            $config->setPath($path);
            $config->setScope('websites');
            $config->setScopeId($stagingWebsite->getId());
            $config->setValue($nodeValue);
            $config->save();
        }

        return $this;
    }

    /**
     * check existing index.php in url
     *
     * @param string $url
     * @return string
     *
     */
    protected function _getIndexedUrl($url)
    {
        $url = rtrim($url, "/");
        if (strpos($url, "index.php") === false) {
            $url .= "/index.php";
        }
        return $url . '/';
    }
}
