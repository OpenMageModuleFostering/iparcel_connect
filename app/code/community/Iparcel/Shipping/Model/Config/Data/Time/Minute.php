<?php
/**
 * Backend model for time minute config field
 *
 * @category	Iparcel
 * @package		Iparcel_Shipping
 * @author		Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_Config_Data_Time_Minute extends Mage_Core_Model_Config_Data{
	/**
	 * Saving minute if proper value
	 */
	public function save(){
		$_minute = $this->getValue();
		if (Mage::helper('shippingip/string')->isInteger($_minute) && $_minute<60 && $_minute>=0){
			return parent::save();
		}else{
			Mage::throwException(Mage::helper('shippingip')->__('Wrong minute'));
		}
	}
}
?>
