<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Grid row url generator
 *
 * @category    Mage
 * @package     Mage_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Model_Widget_Grid_Row_UrlGenerator
{
    /**
     * @var Mage_Backend_Model_Url
     */
    protected $_urlModel;

    /**
     * @var string
     */
    protected $_path;

    /**
     * @var array
     */
    protected $_params;

    /**
     * @var array
     */
    protected $_extraParamsTemplate;

    public function __construct(array $args = array())
    {
        if (!isset($args['urlModel']) || !isset($args['path'])) {
            throw new InvalidArgumentException('Not all required parameters passed');
        }
        $this->_urlModel = $args['urlModel'];
        if (false === ($this->_urlModel instanceof Mage_Backend_Model_Url)) {
            throw new InvalidArgumentException('Passed incorrect parameters');
        }
        $this->_path = (string) $args['path'];
        if (isset($args['params'])) {
            $this->_params = (array) $args['params'];
        }
        if (isset($args['extraParamsTemplate'])) {
            $this->_extraParamsTemplate = (array) $args['extraParamsTemplate'];
        }
    }

    /**
     * Create url for passed item using passed url model
     * @param Varien_Object $item
     * @return string
     */
    public function getUrl($item)
    {
        $params = $this->_prepareParameters($item);
        return $this->_urlModel->getUrl($this->_path, $params);
    }

    /**
     * Convert template params array and merge with preselected params
     * @param $item
     * @return mixed
     */
    protected function _prepareParameters($item)
    {
        $params = array();
        foreach ($this->_extraParamsTemplate as $paramKey => $paramValueMethod) {
            $params[$paramKey] = $item->$paramValueMethod();
        }
        return array_merge($this->_params, $params);
    }
}
