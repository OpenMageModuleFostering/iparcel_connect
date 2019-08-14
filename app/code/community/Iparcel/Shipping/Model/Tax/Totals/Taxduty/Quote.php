<?php 
/**
 * Shipping tax&duty tax for quote total class
 *
 * @category	Iparcel
 * @package		Iparcel_Shipping
 * @author		Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_Tax_Totals_Taxduty_Quote extends Iparcel_Shipping_Model_Tax_Totals_Abstract{
	const TOTAL_CODE = 'shippingip_total_taxduty';
	const XPATH_LABEL = 'iparcel/tax/tax_duty_label';

	/**
	 * Checking if current total is enabled
	 *
	 * @return bool
	 */
	protected function _isEnabled(){
		return $this->_getMode() == Iparcel_Shipping_Model_System_Config_Source_Tax_Mode::CUMULATIVELY;
	}
	
	/**
	 * Adding amount to total
	 */
	protected function _addAmountToTotal(){
		$this->_addAmount($this->_tax+$this->_duty);
		$this->_addBaseAmount($this->_tax+$this->_duty);
	}
	
	/**
	 * Fetching current total to quote 
	 *
	 * @param Mage_Sales_Model_Quote_Address $address
	 * @return Iparcel_Shipping_Model_Tax_Totals_Taxduty_Quote
	 */
	public function fetch(Mage_Sales_Model_Quote_Address $address){
		if ($this->_isEnabled()){
			$quote = $address->getQuote();
			/* var $quote Mage_Sales_Model_Quote */
			// do nothing if it's virtual quote
			if ($quote->isVirtual()){
				return $this;
			}
			// shipping address needed (it's shipping depended tax)
			if (($address->getAddressType() == 'billing')){
				$address = $address->getQuote()->getShippingAddress();
			}
			$this->_setAddress($address);
			$_tax = $address->getShippingipTotalTax();
			$_duty = $address->getShippingipTotalDuty();
			if ($_tax+$_duty){
				$address->addTotal(array(
					'code'	=>	$this->getCode(),
					'title'	=>	$this->_getLabel(),
					'value'	=>	$_tax+$_duty
				));
				
			}
		}
		return $this;
	}
}
