<?php
/**
 * Provides override for completed order's state
 *
 * @category    Iparcel
 * @package     Iparcel_GlobaleCommerce
 * @author      Derek Loevenstein <dloevenstein@i-parcel.com>
 */
class Iparcel_GlobaleCommerce_Model_Sales_Order extends Mage_Sales_Model_Order
{
    /**
     * @return Iparcel_GlobaleCommerce_Model_Sales_Order
     */
    protected function _checkState()
    {
        if (Mage::registry('isExternalSale') && Mage::getStoreConfig('external_api/sales/order_status') != Mage_Sales_Model_Order::STATE_COMPLETE) {
            return $this;
        } else {
            return parent::_checkState();
        }
    }
}
