<?php
/**
 * i-parcel shipping helper for strings
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_Shipping_Helper_String
{
    /**
     * Checking if string has integer value
     *
     * @param string $str
     * @return bool
     */
    public function isInteger(string $str)
    {
        return (int)$str == $str;
    }
}
