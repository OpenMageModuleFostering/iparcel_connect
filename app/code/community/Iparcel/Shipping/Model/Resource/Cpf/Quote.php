<?php
/**
 * Resource Model for Iparcel_Shipping_Model_Cpf_Quote class
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author      Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_Resource_Cpf_Quote extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initializing Resource
     */
    protected function _construct()
    {
        $this->_init('shippingip/cpf_quote', 'id');
    }
}
