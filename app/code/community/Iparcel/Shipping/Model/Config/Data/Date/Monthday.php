<?php
/**
 * Backend model for monthday config field
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_Shipping_Model_Config_Data_Date_Monthday extends Mage_Core_Model_Config_Data
{
    /**
     * Saving monthday if proper value
     */
    public function save()
    {
        $_monthday = $this->getValue();
        if (Mage::helper('shippingip/string')->isInteger($_monthday) && $_monthday<=31 && $_monthday>0) {
            return parent::save();
        } else {
            Mage::throwException(Mage::helper('shippingip')->__('Wrong day of month'));
        }
    }
}
