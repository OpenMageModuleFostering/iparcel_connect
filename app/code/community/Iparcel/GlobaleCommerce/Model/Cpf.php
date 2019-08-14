<?php
/**
 * Db model for cpfs' types
 *
 * @category    Iparcel
 * @package     Iparcel_GlobaleCommerce
 * @author      Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_GlobaleCommerce_Model_Cpf extends Mage_Core_Model_Abstract
{
    /**
     * Initializing model in internal constructor
     */
    protected function _construct()
    {
        $this->_init('ipglobalecommerce/cpf');
    }
    
    /**
     * Processing data before model save
     */
    protected function _beforeSave()
    {
        $this->setCountryCode(strtoupper($this->getCountryCode()));
    }

    /**
     * Load CPF by country code
     *
     * @param string $code, string $attributes
     * @return Iparcel_GlobaleCommerce_Model_Cpf
     */
    public function loadByCountryCode($code, $attributes = '*')
    {
        $collection = $this->getResourceCollection()
            ->addFieldToSelect($attributes)
            ->addFieldToFilter('country_code', $code);
        return $collection->getFirstItem();
    }
}
