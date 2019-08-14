<?php
/**
 * Rewrite of Mage_Sales_Model_Order for i-parcel extension
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author      Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_Sales_Order extends Mage_Sales_Model_Order
{
    /**
     * Check order state before saving
     *
     * @return Iparcel_Shipping_Model_Sales_Order
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
