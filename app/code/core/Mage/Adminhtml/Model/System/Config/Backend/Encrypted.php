<?php

class Mage_Adminhtml_Model_System_Config_Backend_Encrypted extends Mage_Core_Model_Config_Data
{
    protected function _afterLoad()
    {
        if ($this->getValue() && $decrypted = Mage::helper('core')->decrypt($this->getValue())) {
            $this->setValue($decrypted);
        }
    }

    protected function _beforeSave()
    {
        if ($this->getValue() && $encrypted = Mage::helper('core')->encrypt($this->getValue())) {
            $this->setValue($encrypted);
        }
    }
}