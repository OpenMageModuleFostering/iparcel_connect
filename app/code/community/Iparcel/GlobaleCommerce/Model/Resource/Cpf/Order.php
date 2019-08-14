<?php
/**
 * Resource Model for Iparcel_GlobaleCommerce_Model_Cpf_Order class
 *
 * @category    Iparcel
 * @package     Iparcel_GlobaleCommerce
 * @author      Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_GlobaleCommerce_Model_Resource_Cpf_Order extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initializing Resource
     */
    protected function _construct()
    {
        $this->_init('ipglobalecommerce/cpf_order', 'id');
    }
}
