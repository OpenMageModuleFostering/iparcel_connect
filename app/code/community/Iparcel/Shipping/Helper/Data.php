<?php
/**
 * Iparcel_Shipping default data Helper
 *
 * @category		Iparcel
 * @package			Iparcel_Shipping
 * @author			Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Helper_Data extends Mage_Core_Helper_Abstract {
	/**
	 * Getting GUID key
	 *
	 * @return string
	 */
	public function getGuid(){
		return Mage::getStoreConfig('iparcel/config/userid');
	}

	/**
	 * Getting Customer ID
	 *
	 * @return string
	 */
	public function getCustomerID(){
		return Mage::getStoreConfig('iparcel/config/custid');
	}

	/**
	 * Getting external JS Scripts URL
	 *
	 * @return string
	 */
	public function getScriptUrl(){
		return '//script.i-parcel.com/';
	}
}
