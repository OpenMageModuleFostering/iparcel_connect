<?php
/**
 * i-parcel jQuery block
 *
 * @category   Iparcel
 * @package    Iparcel_Shipping
 * @author       Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Block_Html_Head_Jquery extends Mage_Core_Block_Template
{
    /**
     * Checking if i-parcel's jQuery is enabled
     *
     * @return string
     */
    public function getFlag()
    {
        return Mage::getStoreConfigFlag('iparcel/scripts/jquery');
    }
}
