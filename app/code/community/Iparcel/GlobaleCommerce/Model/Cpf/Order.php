<?php
/**
 * Db model for order's cpfs' values
 *
 * @category    Iparcel
 * @package     Iparcel_GlobaleCommerce
 * @author      Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_GlobaleCommerce_Model_Cpf_Order extends Mage_Core_Model_Abstract
{
    /**
     * Initializing model in internal constructor
     */
    protected function _construct()
    {
        $this->_init('ipglobalecommerce/cpf_order');
    }

    /**
     * Load CPF by order_id
     *
     * @param int $orderId, string $fields='*'
     * @return Iparcel_GlobaleCommerce_Model_Cpf_Order
     */
    public function loadByOrderId($orderId, $fields = '*')
    {
        return $this->getCollection()
            ->addFieldToSelect($fields)
            ->addFieldToFilter('order_id', $orderId)
            ->getFirstItem();
    }
}
