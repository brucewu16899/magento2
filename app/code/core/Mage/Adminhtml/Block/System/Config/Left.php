<?php
/**
 * admin config left menu
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Adminhtml_Block_System_Config_Left extends Mage_Adminhtml_Block_Widget
{
    protected $_websiteCode;
    protected $_storeCode;
    protected $_sectionCode;
    
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adminhtml/system/config/left.phtml');
        
        $this->_websiteCode = $this->getRequest()->getParam('website');
        $this->_storeCode   = $this->getRequest()->getParam('store');
        $this->_sectionCode = $this->getRequest()->getParam('section');

    }
    
    public function getLinks()
    {
        $links = array(
            new Varien_Object(array(
                'label' => __('global config'),
                'title' => __('global config title'),
                'url'   => Mage::getUrl('adminhtml/system_config'),
                'class' => is_null($this->_websiteCode) ? 'active' : ''
            ))
        );
        
        $websites = Mage::getConfig()->getNode('global/websites')->asArray();
        foreach ($websites as $code => $info) {
            $links[] = new Varien_Object(array(
                'label' => __($code),
                'url'   => Mage::getUrl('adminhtml/system_config/edit', array('website'=>$code)),
                'class' => ($this->_websiteCode == $code) ? 'active' : ''
            ));
            
            $website = Mage::getModel('core/website')
                ->setCode($code);
            $storeCodes = $website->getStoreCodes();
            foreach ($storeCodes as $storeCode) {
                $links[] = new Varien_Object(array(
                    'label' => __($storeCode),
                    'url'   => Mage::getUrl('adminhtml/system_config/edit', array('website'=>$code, 'store'=>$storeCode)),
                    'class' => ($this->_storeCode == $storeCode) ? 'subitem active' : 'subitem'
                ));
            }
            
            $links[] = new Varien_Object(array(
                'label' => __('new store'),
                'url'   => Mage::getUrl('adminhtml/system_config/edit', array('website'=>$code, 'store'=>'1')),
                'class' => ($this->_storeCode == 1) ? 'subitem active' : 'subitem'
            ));
            
        }
        
        $links[] = new Varien_Object(array(
            'label' => __('new website'),
            'title' => __('new website title'),
            'url'   => Mage::getUrl('adminhtml/system_config/edit', array('website'=>1)),
            'class' => ($this->_websiteCode == 1) ? 'active' : ''
        ));
        
        return $links;
    }

    public function bindBreadcrumbs($breadcrumbs)
    {
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($this->_websiteCode) {
            $breadcrumbs->addLink(__('config'), __('config title'), Mage::getUrl('adminhtml/system_config'));
            if ($this->_storeCode) {
                
            }
            else {
                $breadcrumbs->addLink(($this->_websiteCode == 1) ? __('new website') :__($this->_websiteCode), '');
            }
        }
        else {
            $breadcrumbs->addLink(__('config'), __('config title'));
        }
        return $this;
    }
}
