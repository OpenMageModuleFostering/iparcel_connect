<?php
/**
 * Db model for quote's cpfs' values
 *
 * @category	Iparcel
 * @package		Iparcel_Shipping
 * @author		Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_Cpf_Quote extends Mage_Core_Model_Abstract{
	/**
	 * Initializing model in internal constructor
	 */
	protected function _construct(){
		$this->_init('shippingip/cpf_quote');
	}
	
	/**
	 * Load CPF by quote_id
	 *
	 * @param int $quoteId, string $fields='*'
	 * @return Iparcel_Shipping_Model_Cpf_Quote
	 */
	public function loadByQuoteId($quoteId, $fields='*'){
		return $this->getCollection()
			->addFieldToSelect($fields)
			->addFieldToFilter('quote_id',$quoteId)
			->getFirstItem();
	}
}
?>
