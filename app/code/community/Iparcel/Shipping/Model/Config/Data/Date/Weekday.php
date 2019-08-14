<?php
/**
 * Backend model for weekday config field
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_Shipping_Model_Config_Data_Date_Weekday extends Mage_Core_Model_Config_Data
{
    /**
     * Saving weekday if proper value
     */
    public function save()
    {
        $_weekday = $this->getValue();
        if (Mage::helper('shippingip/string')->isInteger($_weekday) && $_weekday<=7 && $_weekday>0) {
            return parent::save();
        } else {
            Mage::throwException(Mage::helper('shippingip')->__('Wrong day of week'));
        }
    }
}