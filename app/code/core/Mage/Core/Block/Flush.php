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
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */




/**
 * Immediate flush block. To be used only as root
 *
 * @version    1.0 
 * @author     Moshe Gurvich <moshe@varien.com>
 * @author	   Soroka Dmitriy <dmitriy@varien.com>
 * @date       Thu Feb 08 05:56:43 EET 2007
 */

class Mage_Core_Block_Flush extends Mage_Core_Block_Abstract
{
	function toHtml()
	{
		if (!$this->_beforeToHtml()) {
			return '';
		}

	    ob_implicit_flush();
	    
	    $list = $this->getData('sorted_children_list');
	    if (!empty($list)) {
    	    foreach ($list as $name) {
    	        $block = $this->getLayout()->getBlock($name);
    	        if (!$block) {
    	            Mage::exception(__('Invalid block: %s', $name));
    	        }
    	        echo $block->toHtml();
    	    }
	    }
	}
}// Class Mage_Core_Block_List END
