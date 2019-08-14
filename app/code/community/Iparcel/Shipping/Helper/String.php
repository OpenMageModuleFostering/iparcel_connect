<?php
/**
 * i-parcel shipping helper for strings
 *
 * @category	Iparcel
 * @package		Iparcel_Shipping
 * @author		Patryk Grudniewski <patryk.grudniewski@sabiocoders.com>
 */
class Iparcel_Shipping_Helper_String{
	/**
	 * Checking if string has integer value
	 *
	 * @param string $str
	 * @return bool
	 */
	public function isInteger(string $str){
		return (int)$str == $str;
	}
}
?>
