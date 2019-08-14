<?php
/**
 * Db model for i-parcel's tax intercepting
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author      Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_Tax_Totals extends Mage_Core_Model_Abstract
{
    /**
     * Initializing model in internal constructor
     */
    protected function _construct()
    {
        $this->_init('shippingip/tax_totals');
    }
    
    /**
     * Load totals by order_id
     *
     * @param int $orderId, string $fields='*'
     * @return Iparcel_Shipping_Model_Tax_Totals
     */
    public function loadByOrderId($orderId, $attributes = '*')
    {
        $collection = $this->getResourceCollection()
            ->addFieldToSelect($attributes)
            ->addFieldToFilter('order_id', $orderId);
        return $collection->getFirstItem();
    }
}
