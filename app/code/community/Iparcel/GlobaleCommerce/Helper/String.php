<?php
/**
 * i-parcel shipping helper for strings
 *
 * @category    Iparcel
 * @package     Iparcel_GlobaleCommerce
 * @author      Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_GlobaleCommerce_Helper_String
{
    /**
     * Checking if string has integer value
     *
     * @param string $str
     * @return bool
     */
    public function isInteger($str)
    {
        return (int)$str == $str;
    }
}
