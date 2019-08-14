<?php
/**
 * Resource Model for Iparcel_Shipping_Model_Api_External_Sales_Order class
 *
 * @category    Iparcel
 * @package     Iparcel_Shipping
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_Shipping_Model_Resource_Api_Order extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initializing Resource
     */
    protected function _construct()
    {
        $this->_init('shippingip/api_order', 'id');
    }
}
