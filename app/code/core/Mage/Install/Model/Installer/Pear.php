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
 * @package    Mage_Install
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

require_once "Varien/Pear/Package.php";

/**
 * PEAR Packages Download Manager
 *
 * @category   Mage
 * @package    Mage_Install
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Mage_Install_Model_Installer_Pear
{
    public function getPackages()
    {
        $packages = array(
            'pear/PEAR-stable',
            'mage-core/Mage_Pear_Helpers',
            'mage-core/Lib_ZF',
            'mage-core/Lib_Varien',
            'mage-core/Mage_All',
            'mage-core/Interface_Frontend_Default',
            'mage-core/Interface_Adminhtml_Default'
        );
        return $packages;
    }

    public function checkDownloads()
    {
        $pear = new Varien_Pear;
        $pkg = new PEAR_PackageFile($pear->getConfig(), false);
        $result = true;
        foreach ($this->getPackages() as $package) {
            $obj = $pkg->fromAnyFile($package, PEAR_VALIDATE_NORMAL);
            if (PEAR::isError($obj)) {
                $uinfo = $obj->getUserInfo();
                if (is_array($uinfo)) {
                    foreach ($uinfo as $message) {
                        if (is_array($message)) {
                            $message = $message['message'];
                        }
                        Mage::getSingleton('install/session')->addError($message);
                    }
                } else {
                    print_r($obj->getUserInfo());
                    #Mage::getSingleton('install/session')->addError($message);
                }
                $result = false;
            }
        }
        return $result;
    }
}