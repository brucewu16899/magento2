<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sitemap
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Sitemap model
 *
 * @method Mage_Sitemap_Model_Resource_Sitemap _getResource()
 * @method Mage_Sitemap_Model_Resource_Sitemap getResource()
 * @method string getSitemapType()
 * @method Mage_Sitemap_Model_Sitemap setSitemapType(string $value)
 * @method string getSitemapFilename()
 * @method Mage_Sitemap_Model_Sitemap setSitemapFilename(string $value)
 * @method string getSitemapPath()
 * @method Mage_Sitemap_Model_Sitemap setSitemapPath(string $value)
 * @method string getSitemapTime()
 * @method Mage_Sitemap_Model_Sitemap setSitemapTime(string $value)
 * @method int getStoreId()
 * @method Mage_Sitemap_Model_Sitemap setStoreId(int $value)
 *
 * @category    Mage
 * @package     Mage_Sitemap
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sitemap_Model_Sitemap extends Mage_Core_Model_Abstract
{
    /**
     * Real file path
     *
     * @var string
     */
    protected $_filePath;

    /**
     * File handler
     *
     * @var Varien_Io_File
     */
    protected $_fileHandler;

    /**
     * Sitemap items
     *
     * @var array
     */
    protected $_sitemapItems = array();

    /**
     * Current sitemap increment
     *
     * @var int
     */
    protected $_sitemapIncrement = 0;

    /**
     * Sitemap start and end tags
     *
     * @var array
     */
    protected $_tags = array();

    /**
     * Number of lines in sitemap
     *
     * @var int
     */
    protected $_lineCount = 0;

    /**
     * Current sitemap file size
     *
     * @var int
     */
    protected $_fileSize = 0;

    /**
     * Init model
     */
    protected function _construct()
    {
        $this->_init('Mage_Sitemap_Model_Resource_Sitemap');

        $this->_tags = array(
            'start' => '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL
                . '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">',
            'end' => '</urlset>'
        );
    }

    /**
     * Get file handler
     *
     * @throws Mage_Core_Exception
     * @return Varien_Io_File
     */
    protected function _getFileHandler()
    {
        if ($this->_fileHandler) {
            return $this->_fileHandler;
        } else {
            Mage::throwException(Mage::helper('Mage_Sitemap_Helper_Data')->__('File handler unreachable'));
        }
    }

    /**
     * Initialize sitemap items
     */
    protected function _initSitemapItems()
    {
        /** @var $helper Mage_Sitemap_Helper_Data */
        $helper = Mage::helper('Mage_Sitemap_Helper_Data');
        $storeId = $this->getStoreId();

        $this->_sitemapItems[] = new Varien_Object(array(
            'changefreq' => $helper->getCategoryChangefreq($storeId),
            'priority' => $helper->getCategoryPriority($storeId),
            'collection' => Mage::getResourceModel('Mage_Sitemap_Model_Resource_Catalog_Category')
                ->getCollection($storeId)
        ));

        $this->_sitemapItems[] = new Varien_Object(array(
            'changefreq' => $helper->getProductChangefreq($storeId),
            'priority' => $helper->getProductPriority($storeId),
            'collection' => Mage::getResourceModel('Mage_Sitemap_Model_Resource_Catalog_Product')
                ->getCollection($storeId)
        ));

        $this->_sitemapItems[] = new Varien_Object(array(
            'changefreq' => $helper->getPageChangefreq($storeId),
            'priority' => $helper->getPagePriority($storeId),
            'collection' => Mage::getResourceModel('Mage_Sitemap_Model_Resource_Cms_Page')->getCollection($storeId)
        ));
    }

    /**
     * Check sitemap file location and permissions
     *
     * @throws Mage_Core_Exception
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $file = new Varien_Io_File();
        $realPath = $file->getCleanPath(Mage::getBaseDir() . '/' . $this->getSitemapPath());

        /**
         * Check path is allow
         */
        /** @var $helper Mage_Sitemap_Helper_Data */
        $helper = Mage::helper('Mage_Sitemap_Helper_Data');
        if (!$file->allowedPath($realPath, Mage::getBaseDir())) {
            Mage::throwException($helper->__('Please define correct path'));
        }
        /**
         * Check exists and writeable path
         */
        if (!$file->fileExists($realPath, false)) {
            Mage::throwException($helper->__('Please create the specified folder "%s" before saving the sitemap.',
                Mage::helper('Mage_Core_Helper_Data')->escapeHtml($this->getSitemapPath())));
        }

        if (!$file->isWriteable($realPath)) {
            Mage::throwException($helper->__('Please make sure that "%s" is writable by web-server.',
                $this->getSitemapPath()));
        }
        /**
         * Check allow filename
         */
        if (!preg_match('#^[a-zA-Z0-9_\.]+$#', $this->getSitemapFilename())) {
            Mage::throwException($helper->__('Please use only letters (a-z or A-Z), numbers (0-9) or underscore (_) in the filename. No spaces or other characters are allowed.'));
        }
        if (!preg_match('#\.xml$#', $this->getSitemapFilename())) {
            $this->setSitemapFilename($this->getSitemapFilename() . '.xml');
        }

        $this->setSitemapPath(rtrim(str_replace(str_replace('\\', '/', Mage::getBaseDir()), '', $realPath), '/') . '/');

        return parent::_beforeSave();
    }

    /**
     * Return real file path
     *
     * @return string
     */
    protected function _getPath()
    {
        if (is_null($this->_filePath)) {
            $this->_filePath = str_replace('//', '/', Mage::getBaseDir() . $this->getSitemapPath());
        }
        return $this->_filePath;
    }

    /**
     * Generate XML file
     *
     * @return Mage_Sitemap_Model_Sitemap
     */
    public function generateXml()
    {
        $this->_initSitemapItems();
        /** @var $sitemapItem Varien_Object */
        foreach ($this->_sitemapItems as $sitemapItem) {
            $changefreq = $sitemapItem->getChangefreq();
            $priority = $sitemapItem->getPriority();
            foreach ($sitemapItem->getCollection() as $item) {
                $xml = $this->_getSitemapRow($item->getUrl(), $item->getUpdatedAt(), $changefreq, $priority);
                if ($this->_isSplitRequired($xml) && $this->_sitemapIncrement > 0) {
                    $this->_finalizeSitemap();
                }
                if (!$this->_fileSize) {
                    $this->_createSitemap();
                }
                $this->_writeSitemapRow($xml);
                // Increase counters
                $this->_lineCount++;
                $this->_fileSize += strlen($xml);
            }
        }
        $this->_finalizeSitemap();

        if ($this->_sitemapIncrement == 1) {

            $this->_getFileHandler()
                ->mv($this->_getCurrentSitemapFilename($this->_sitemapIncrement), $this->getSitemapFilename());
        } else {
            $this->_createSitemapIndex();
        }

        $this->setSitemapTime(Mage::getSingleton('Mage_Core_Model_Date')->gmtDate('Y-m-d H:i:s'));
        $this->save();

        return $this;
    }

    protected function _createSitemapIndex()
    {
        $this->_createSitemap($this->getSitemapFilename());
        for ($i = 1; $i <= $this->_sitemapIncrement; $i++) {
            $date = new Varien_Date();
            $path = ltrim($this->getSitemapPath(), '/') . '/';
            $url =  $path . $this->_getCurrentSitemapFilename($i);
            $xml = $this->_getSitemapRow($url, $date->now());
            $this->_writeSitemapRow($xml);
        }
        $this->_finalizeSitemap();
    }

    /**
     * Check is split required
     *
     * @param string $row
     * @return bool
     */
    protected function _isSplitRequired($row)
    {
        /** @var $helper Mage_Sitemap_Helper_Data */
        $helper = Mage::helper('Mage_Sitemap_Helper_Data');
        $storeId = $this->getStoreId();
        if ($this->_lineCount + 1 > $helper->getMaximumLinesNumber($storeId)) {
            return true;
        }

        if ($this->_fileSize + strlen($row) > $helper->getMaximumFileSize($storeId)) {
            return true;
        }

        return false;
    }

    /**
     * Get sitemap row
     *
     * @param string $url
     * @param string $lastmod
     * @param string $changefreq
     * @param string $priority
     * @return string
     */
    protected function _getSitemapRow($url, $lastmod = null, $changefreq = null, $priority = null)
    {
        $baseUrl = Mage::app()->getStore($this->getStoreId())->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_LINK);
        $url = str_replace('//', '/', $baseUrl . $url);
        $row = '<loc>' . htmlspecialchars($url) . '</loc>';
        if ($lastmod) {
            $row .= '<lastmod>' . date('c', strtotime($lastmod)) . '</lastmod>';
        }
        if ($changefreq) {
            $row .= '<changefreq>' . $changefreq . '</changefreq>';
        }
        if ($priority) {
            $row .= sprintf('<priority>%.1f</priority>', $priority);
        }

        return '<url>' . $row . '</row>';
    }

    /**
     * Create new sitemap file
     *
     * @param string $fileName
     */
    protected function _createSitemap($fileName = null)
    {
        if (!$fileName) {
            $this->_sitemapIncrement++;
            $fileName = $this->_getCurrentSitemapFilename($this->_sitemapIncrement);
        }
        $this->_fileHandler = new Varien_Io_File();
        $this->_fileHandler->setAllowCreateFolders(true);
        $this->_fileHandler->open(array('path' => $this->_getPath()));

        if ($this->_fileHandler->fileExists($fileName) && !$this->_fileHandler->isWriteable($fileName)) {
            Mage::throwException(Mage::helper('Mage_Sitemap_Helper_Data')
                ->__('File "%s" cannot be saved. Please, make sure the directory "%s" is writable by web server.',
                    $fileName, $this->_getPath()
                )
            );
        }

        $this->_fileHandler->streamOpen($fileName);
        $this->_fileHandler->streamWrite($this->_tags['start']);

        $this->_fileSize = strlen(implode('', $this->_tags));
    }

    /**
     * Write sitemap row
     *
     * @param string $row
     */
    protected function _writeSitemapRow($row)
    {
        $this->_getFileHandler()->streamWrite($row);
    }

    protected function _finalizeSitemap()
    {
        $this->_fileHandler->streamWrite($this->_tags['end']);
        $this->_fileHandler->streamClose();

        // Reset all counters
        $this->_lineCount = 0;
        $this->_fileSize = 0;
    }

    /**
     * Get current sitemap filename
     *
     * @param int $index
     * @return string
     */
    protected function _getCurrentSitemapFilename($index)
    {
        return 'sitemap-' . $index . '.xml';
    }
}
