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
 * Base widget class
 *
 * @category   Mage
 * @package    Mage_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Backend_Block_Widget extends Mage_Backend_Block_Template
{
    /**
     * @var Mage_Core_Model_Helper_Registry
     */
    protected $_helperRegistry;

    /**
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->_helperRegistry = isset($data['helperRegistry'])
            ? isset($data['helperRegistry'])
            : null;

        parent::__construct($data);
    }

    /**
     * Get helper registry object
     *
     * @return Mage_Core_Model_Helper_Registry
     */
    protected function _getHelperRegistry()
    {
        if (null === $this->_helperRegistry) {
            $this->_helperRegistry = Mage::getSingleton('Mage_Core_Model_Helper_Registry');
        }
        return $this->_helperRegistry;
    }


    public function getId()
    {
        if ($this->getData('id')===null) {
            $this->setData('id', Mage::helper('Mage_Core_Helper_Data')->uniqHash('id_'));
        }
        return $this->getData('id');
    }

    public function getHtmlId()
    {
        return $this->getId();
    }

    /**
     * Get current url
     *
     * @param array $params url parameters
     * @return string current url
     */
    public function getCurrentUrl($params = array())
    {
        if (!isset($params['_current'])) {
            $params['_current'] = true;
        }
        return $this->getUrl('*/*/*', $params);
    }

    protected function _addBreadcrumb($label, $title=null, $link=null)
    {
        $this->getLayout()->getBlock('breadcrumbs')->addLink($label, $title, $link);
    }

    /**
     * Create button and return its html
     *
     * @param string $label
     * @param string $onclick
     * @param string $class
     * @param string $id
     * @return string
     */
    public function getButtonHtml($label, $onclick, $class='', $id=null) {
        //@todo: Add UI id to button
        return $this->getLayout()->createBlock('Mage_Backend_Block_Widget_Button', $this->getNameInLayout() .'.'. '-button')
            ->setData(array(
                'label'     => $label,
                'onclick'   => $onclick,
                'class'     => $class,
                'type'      => 'button',
                'id'        => $id,
            ))
            ->toHtml();
    }

    public function getGlobalIcon()
    {
        return '<img src="'.$this->getSkinUrl('images/fam_link.gif').'" alt="'.$this->__('Global Attribute').'" title="'.$this->__('This attribute shares the same value in all the stores').'" class="attribute-global"/>';
    }
}

