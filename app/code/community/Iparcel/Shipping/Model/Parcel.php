<?php
/**
 * Db model for cpfs' types
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_Shipping_Model_Parcel extends Mage_Core_Model_Abstract
{
    /**
     * Initializing model in internal constructor
     */
    protected function _construct()
    {
        $this->_init('shippingip/parcel');
    }
    
    /**
     * Load Parcel by Order ID
     *
     * @param string $orderId, string $attributes
     * @return Iparcel_Shipping_Model_Cpf
     */
    public function loadByOrderId($orderId, $attributes = '*')
    {
        $collection = $this->getResourceCollection()
            ->addFieldToSelect($attributes)
            ->addFieldToFilter('order_id', $orderId);
        return $collection->getFirstItem();
    }
}
