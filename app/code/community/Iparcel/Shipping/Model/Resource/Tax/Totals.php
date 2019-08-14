<?php
/**
 * Resource Model for Iparcel_Shipping_Model_Tax_Totals class
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author      Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_Resource_Tax_Totals extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initializing Resource
     */
    protected function _construct()
    {
        $this->_init('shippingip/tax_totals', 'id');
    }
}
