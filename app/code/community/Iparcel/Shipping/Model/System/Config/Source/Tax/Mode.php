<?php
/**
 * Source model for iparcel/tax/mode config field
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_Shipping_Model_System_Config_Source_Tax_Mode
{
    const DISABLED = "0";
    const CUMULATIVELY = "1";
    const SEPARATELY = "2";

    /**
     * Options list
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => self::DISABLED, 'label' => Mage::helper('shippingip')->__('Disabled')),
            array('value' => self::CUMULATIVELY, 'label' => Mage::helper('shippingip')->__('Enabled - Tax and Duty cumulatively')),
            array('value' => self::SEPARATELY, 'label' => Mage::helper('shippingip')->__('Enabled - Tax and Duty separately'))
        );
    }
}
