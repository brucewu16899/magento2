<?php

#include_once 'Ecom/Core/Block/Text.php';

/**
 * Base html block
 *
 * @copyright  Varien, 2007
 * @version    1.0 
 * @author	   Moshe Gurvich <moshe@varien.com>
 * @date       Thu Feb 08 05:56:43 EET 2007
 */

class Ecom_Core_Block_Text_ListLink extends Ecom_Core_Block_Text
{
	function setLink($liParams, $aParams, $innerText, $afterText='&nbsp;|')
	{
	    $this->setAttribute('liParams', $liParams);
	    $this->setAttribute('aParams', $aParams);
	    $this->setAttribute('innerText', $innerText);
	    $this->setAttribute('afterText', $afterText);
	    return $this;
	}

	function toHtml()
	{
	    $this->setText('<li');
	    $params = $this->getAttribute('liParams');
	    if (!empty($params) && is_array($params)) {
	        foreach ($params as $key=>$value) {
	            $this->addText(' '.$key.'="'.addslashes($value).'"');
	        }
	    } elseif (is_string($params)) {
	        $this->addText(' '.$params);
	    }
	    $this->addText('><a');

	    $params = $this->getAttribute('aParams');
	    if (!empty($params) && is_array($params)) {
	        foreach ($params as $key=>$value) {
	            $this->addText(' '.$key.'="'.addslashes($value).'"');
	        }
	    } elseif (is_string($params)) {
	        $this->addText(' '.$params);
	    }

	    $this->addText('>'.$this->getAttribute('innerText').'</a>'.$this->getAttribute('afterText').'</li>'."\r\n");
	    
	    return parent::toHtml();
	}
}// Class Ecom_Core_Block_List END