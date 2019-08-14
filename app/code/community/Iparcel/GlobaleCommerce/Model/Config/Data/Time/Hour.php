<?php
/**
 * Backend model for time hour config field
 *
 * @category    Iparcel
 * @package     Iparcel_GlobaleCommerce
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_GlobaleCommerce_Model_Config_Data_Time_Hour extends Mage_Core_Model_Config_Data
{
    /**
     * Saving hour if proper value
     */
    public function save()
    {
        $_hour = $this->getValue();
        if (Mage::helper('ipglobalecommerce/string')->isInteger($_hour) && $_hour<24 && $_hour>=0) {
            return parent::save();
        } else {
            Mage::throwException(Mage::helper('ipglobalecommerce')->__('Wrong hour'));
        }
    }
}
