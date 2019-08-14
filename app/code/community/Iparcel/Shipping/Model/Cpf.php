<?php
/**
 * Db model for cpfs' types
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author      Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_Cpf extends Mage_Core_Model_Abstract
{
    /**
     * Initializing model in internal constructor
     */
    protected function _construct()
    {
        $this->_init('shippingip/cpf');
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
     * @return Iparcel_Shipping_Model_Cpf
     */
    public function loadByCountryCode($code, $attributes = '*')
    {
        $collection = $this->getResourceCollection()
            ->addFieldToSelect($attributes)
            ->addFieldToFilter('country_code', $code);
        return $collection->getFirstItem();
    }
}
