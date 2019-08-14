<?php
/**
 * Iparcel_Shipping default data Helper
 *
 * @category        Iparcel
 * @package             Iparcel_Shipping
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_Shipping_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Getting GUID key
     *
     * @return string
     */
    public function getGuid()
    {
        return Mage::getStoreConfig('iparcel/config/userid');
    }

    /**
     * Getting Customer ID
     *
     * @return string
     */
    public function getCustomerID()
    {
        return Mage::getStoreConfig('iparcel/config/custid');
    }

    /**
     * Getting external JS Scripts URL
     *
     * @return string
     */
    public function getScriptUrl()
    {
        return '//script.i-parcel.com/';
    }

    /**
     * Escape quotation mark in strings for inclusion in JavaScript objects
     *
     * @param string $string String to escape
     * @return string
     */
    public function jsEscape($string = '')
    {
        return addcslashes($string, "\"");
    }
}
