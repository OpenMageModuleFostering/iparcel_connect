<?php
/**
 * External API Helper
 *
 * @category	Iparcel
 * @package		Iparcel_Shipping
 * @author		Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Helper_Api_External{
	/*
	 * var $_allowedAddr array
	 *
	 * List of allowed addresses and subnets
	 */
	protected $_allowedAddr = array(
		'47.19.112.64/27',
		'23.96.0.0/18',
		'23.96.64.0/28',
		'23.96.64.64/26',
		'23.96.64.128/27',
		'23.96.64.160/28',
		'23.96.80.0/20',
		'23.96.96.0/19',
		'23.100.16.0/20',
		'137.116.112.0/20',
		'137.117.32.0/19',
		'137.117.64.0/18',
		'137.135.64.0/18',
		'157.56.176.0/21',
		'168.61.32.0/20',
		'168.61.48.0/21',
		'168.62.32.0/19',
		'168.62.160.0/19',
		'138.91.96.0/25',
		'138.91.96.128/26',
		'138.91.96.192/28',
		'138.91.112.0/20',
		'191.234.32.0/19',
		'191.236.0.0/19',
		'191.238.0.0/25',
		'191.238.0.128/26',
		'191.238.0.192/23',
		'191.238.1.0/24',
		'191.238.2.0/23',
		'191.238.4.0/24',
		'191.238.8.0/21',
		'191.238.16.0/20',
		'191.238.32.0/19',	
	);

	/**
	 * Checking if ip address matches range
	 *
	 * @param string $ip, string $range
	 * @return bool
	 */
	protected function _cidrMatch ($ip, $range){
		list ($subnet, $bits) = explode('/', $range);
		$ip = ip2long($ip);
		$subnet = ip2long($subnet);
		$mask = -1 << (32 - $bits);
		$subnet &= $mask;
		return ($ip & $mask) == $subnet;
	}

	/**
	 * Checking if request ip address is allowed
	 *
	 * @return bool
	 */
	public function isAllowed(){
		return true;
		$ip = Mage::helper('core/http')->getRemoteAddr();
		foreach ($this->_allowedAddr as $range){
			if ($this->_cidrMatch($ip, $range)){
				return true;
			}
		}
		return false;
	}
}
?>
