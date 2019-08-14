<?php
/**
 * Resource Collection Model for Iparcel_Shipping_Model_Cpf class
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_Shipping_Model_Resource_Cpf_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initializing Resource Collection
     */
    public function _construct()
    {
        $this->_init('shippingip/cpf');
    }
}
