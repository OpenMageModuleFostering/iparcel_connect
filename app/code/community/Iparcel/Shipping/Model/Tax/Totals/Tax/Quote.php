<?php
/**
 * Shipping tax tax for quote total class
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author      Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_Tax_Totals_Tax_Quote extends Iparcel_Shipping_Model_Tax_Totals_Abstract
{
    const XPATH_LABEL = 'iparcel/tax/tax_label';
    const TOTAL_CODE = 'shippingip_total_tax';
    
    /**
     * Checking if current total is enabled
     *
     * @return bool
     */
    protected function _isEnabled()
    {
        return $this->_getMode() == Iparcel_Shipping_Model_System_Config_Source_Tax_Mode::SEPARATELY;
    }

    /**
     * Adding amount to total
     */
    protected function _addAmountToTotal()
    {
        $this->_addAmount($this->_tax);
        $this->_addBaseAmount($this->_tax);
    }
    
    /**
     * Fetching current total to quote
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Iparcel_Shipping_Model_Tax_Totals_Tax_Quote
     */
    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        if ($this->_isEnabled() && !$this->_isDomestic($address)) {
            $quote = $address->getQuote();
            /* var $quote Mage_Sales_Model_Quote */
            // do nothing if it's virtual quote
            if ($quote->isVirtual()) {
                return $this;
            }
            // shipping address needed (it's shipping depended tax)
            if (($address->getAddressType() == 'billing')) {
                $address = $address->getQuote()->getShippingAddress();
            }
            $this->_setAddress($address);
            $_tax = $address->getShippingipTotalTax();
            if ($_tax) {
                $address->addTotal(array(
                    'code'  =>  $this->getCode(),
                    'title'     =>  $this->_getLabel(),
                    'value'     =>  $_tax
                ));
            }
        }
        return $this;
    }
}
