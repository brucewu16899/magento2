<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Extension model
 *
 * @category    Mage
 * @package     Mage_Connect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Connect_Model_Extension extends Varien_Object
{
    /**
    * Cache for targets
    *
    * @var array
    */
    protected $_targets;

    /**
    * Internal cache for package
    *
    * @var Mage_Connect_Package
    */
    protected $_package;

    /**
     * Return package object
     *
     * @return Mage_Connect_Package
     */
    protected function getPackage()
    {
        if (!$this->_package instanceof Mage_Connect_Package) {
            $this->_package = new Mage_Connect_Package();
        }
        return $this->_package;
    }

    /**
    * Set package object.
    *
    * @return Mage_Connect_Model_Extension
    */
    public function generatePackageXml()
    {
        Mage::getSingleton('Mage_Connect_Model_Session')
            ->setLocalExtensionPackageFormData($this->getData());

        $this->_setPackage()
            ->_setRelease()
            ->_setAuthors()
            ->_setDependencies()
            ->_setContents();
        if (!$this->getPackage()->validate()) {
            $message = $this->getPackage()->getErrors();
            throw Mage::exception('Mage_Core', Mage::helper('Mage_Connect_Helper_Data')->__($message[0]));
        }
        $this->setPackageXml($this->getPackage()->getPackageXml());
        return $this;
    }

    /**
    * Set general information.
    *
    * @return Mage_Connect_Model_Extension
    */
    protected function _setPackage()
    {
        $this->getPackage()
            ->setName($this->getData('name'))
            ->setChannel($this->getData('channel'))
            ->setLicense($this->getData('license'), $this->getData('license_uri'))
            ->setSummary($this->getData('summary'))
            ->setDescription($this->getData('description'));
        return $this;
    }

    /**
    * Set release information
    *
    * @return Mage_Connect_Model_Extension
    */
    protected function _setRelease()
    {
        $this->getPackage()
            ->setDate(date('Y-m-d'))
            ->setTime(date('H:i:s'))
            ->setVersion($this->getData('version')?$this->getData('version'):$this->getData('release_version'))
            ->setStability($this->getData('stability'))
            ->setNotes($this->getData('notes'));
        return $this;
    }

    /**
    * Set authors
    *
    * @return Mage_Connect_Model_Extension
    */
    protected function _setAuthors()
    {
        $authors = $this->getData('authors');
        foreach ($authors['name'] as $i => $name) {
            $user  = $authors['user'][$i];
            $email = $authors['email'][$i];
            $this->getPackage()->addAuthor($name, $user, $email);
        }
        return $this;
    }


    protected function packageFilesToArray($filesString)
    {
        $packageFiles = array();
        if($filesString) {
            $filesArray = preg_split("/[\n\r]+/", $filesString);
            foreach($filesArray as $file) {
                $file = trim($file, "/");
                $res = explode(DIRECTORY_SEPARATOR, $file, 2);
                array_map('trim', $res);
                if(2 == count($res)) {
                    $packageFiles[] = array('target'=>$res[0], 'path'=>$res[1]);
                }
            }
        }
        return $packageFiles;
    }

    /**
    * Set php, php extensions, another packages dependencies
    *
    * @return Mage_Connect_Model_Extension
    */
    protected function _setDependencies()
    {
        $this->getPackage()
            ->clearDependencies()
            ->setDependencyPhpVersion($this->getData('depends_php_min'), $this->getData('depends_php_max'));

        foreach ($this->getData('depends') as $deptype=>$deps) {
            foreach ($deps['name'] as $i=>$type) {
                if (0===$i) {
                    continue;
                }
                $name = $deps['name'][$i];
                $min = !empty($deps['min'][$i]) ? $deps['min'][$i] : false;
                $max = !empty($deps['max'][$i]) ? $deps['max'][$i] : false;

                $files = !empty($deps['files'][$i]) ? $deps['files'][$i] : false;
                $packageFiles = $this->packageFilesToArray($files);

                if ($deptype !== 'extension') {
                    $channel = !empty($deps['channel'][$i])
                        ? $deps['channel'][$i]
                        : 'connect.magentocommerce.com/core';
                }
                switch ($deptype) {
                    case 'package':
                        $this->getPackage()->addDependencyPackage($name, $channel, $min, $max, $packageFiles);
                        break;

                    case 'extension':
                        $this->getPackage()->addDependencyExtension($name, $min, $max);
                        break;
                }
            }
        }
        return $this;
    }

    /**
    * Set contents. Add file or entire directory.
    *
    * @return Mage_Connect_Model_Extension
    */
    protected function _setContents()
    {
        $this->getPackage()->clearContents();
        $contents = $this->getData('contents');
        foreach ($contents['target'] as $i=>$target) {
            if (0===$i) {
                continue;
            }
            switch ($contents['type'][$i]) {
                case 'file':
                    $this->getPackage()->addContent($contents['path'][$i], $contents['target'][$i]);
                    break;

                case 'dir':
                    $target = $contents['target'][$i];
                    $path = $contents['path'][$i];
                    $include = $contents['include'][$i];
                    $ignore = $contents['ignore'][$i];
                    $this->getPackage()->addContentDir($target, $path, $ignore, $include);
                    break;
            }
        }
        return $this;
    }

    /**
    * Save package file to var/connect.
    *
    * @return boolean
    */
    public function savePackage()
    {
        if ($this->getData('file_name') != '') {
            $fileName = $this->getData('file_name');
            $this->unsetData('file_name');
        } else {
            $fileName = $this->getName();
        }

        if (!preg_match('/^[a-z0-9]+[a-z0-9\-\_\.]*([\/\\\\]{1}[a-z0-9]+[a-z0-9\-\_\.]*)*$/i', $fileName)) {
            return false;
        }

        if (!$this->getPackageXml()) {
            $this->generatePackageXml();
        }
        if (!$this->getPackageXml()) {
            return false;
        }

        $path = Mage::helper('Mage_Connect_Helper_Data')->getLocalPackagesPath();
        if (!@file_put_contents($path . 'package.xml', $this->getPackageXml())) {
            return false;
        }

        $this->unsPackageXml();
        $this->unsTargets();
        $xml = Mage::helper('Mage_Core_Helper_Data')->assocToXml($this->getData());
        $xml = new Varien_Simplexml_Element($xml->asXML());

        // prepare dir to save
        $parts = explode(DS, $fileName);
        array_pop($parts);
        $newDir = implode(DS, $parts);
        if ((!empty($newDir)) && (!is_dir($path . $newDir))) {
            if (!@mkdir($path . $newDir, 0777, true)) {
                return false;
            }
        }

        if (!@file_put_contents($path . $fileName . '.xml', $xml->asNiceXml())) {
            return false;
        }

        return true;
    }

    /**
    * Create package file
    *
    * @return boolean
    */
    public function createPackage()
    {
        $path = Mage::helper('Mage_Connect_Helper_Data')->getLocalPackagesPath();
        if (!Mage::getConfig()->createDirIfNotExists($path)) {
            return false;
        }
        if (!$this->getPackageXml()) {
            $this->generatePackageXml();
        }
        $this->getPackage()->save($path);
        return true;
    }

    /**
    * Create package file compatible with previous version of Magento Connect Manager
    *
    * @return boolean
    */
    public function createPackageV1x()
    {
        $path = Mage::helper('Mage_Connect_Helper_Data')->getLocalPackagesPathV1x();
        if (!Mage::getConfig()->createDirIfNotExists($path)) {
            return false;
        }
        if (!$this->getPackageXml()) {
            $this->generatePackageXml();
        }
        $this->getPackage()->saveV1x($path);
        return true;
    }

    /**
    * Retrieve stability value and name for options
    *
    * @return array
    */
    public function getStabilityOptions()
    {
        return array(
            'devel'     => 'Development',
            'alpha'     => 'Alpha',
            'beta'      => 'Beta',
            'stable'    => 'Stable',
        );
    }

    /**
    * Retrieve targets
    *
    * @return array
    */
    public function getLabelTargets()
    {
        if (!is_array($this->_targets)) {
            $objectTarget = new Mage_Connect_Package_Target();
            $this->_targets = $objectTarget->getLabelTargets();
        }
        return $this->_targets;
    }

}
