<?php
/**
 * Resource Collection Model for Iparcel_GlobaleCommerce_Model_Cpf_Quote class
 *
 * @category    Iparcel
 * @package     Iparcel_GlobaleCommerce
 * @author      Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_GlobaleCommerce_Model_Resource_Cpf_Quote_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initializing Resource Collection
     */
    public function _construct()
    {
        $this->_init('ipglobalecommerce/cpf_quote');
    }
}
