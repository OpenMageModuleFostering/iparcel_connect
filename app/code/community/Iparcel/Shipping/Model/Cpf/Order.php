<?php
/**
 * Db model for order's cpfs' values
 *
 * @category	Iparcel
 * @package		Iparcel_Shipping
 * @author		Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_Cpf_Order extends Mage_Core_Model_Abstract{
	/**
	 * Initializing model in internal constructor
	 */
	protected function _construct(){
		$this->_init('shippingip/cpf_order');
	}

	/**
	 * Load CPF by order_id
	 *
	 * @param int $orderId, string $fields='*'
	 * @return Iparcel_Shipping_Model_Cpf_Order
	 */
	public function loadByOrderId($orderId, $fields='*'){
		return $this->getCollection()
			->addFieldToSelect($fields)
			->addFieldToFilter('order_id',$orderId)
			->getFirstItem();
	}
}
?>
