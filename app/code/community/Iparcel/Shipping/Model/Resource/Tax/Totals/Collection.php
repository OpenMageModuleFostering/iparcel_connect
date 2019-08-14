<?php
/**
 * Resource Collection Model for Iparcel_Shipping_Model_Tax_Totals class
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_Shipping_Model_Resource_Tax_Totals_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initializing Resource Collection
     */
    protected function _construct()
    {
        $this->_init('shippingip/tax_totals');
    }
}
