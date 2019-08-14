<?php
/**
 * Backend model for time hour config field
 *
 * @category	Iparcel
 * @package		Iparcel_Shipping
 * @author		Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_Config_Data_Time_Hour extends Mage_Core_Model_Config_Data{
	/**
	 * Saving hour if proper value
	 */
	public function save(){
		$_hour = $this->getValue();
		if (Mage::helper('shippingip/string')->isInteger($_hour) && $_hour<24 && $_hour>=0){
			return parent::save();
		}else{
			Mage::throwException(Mage::helper('shippingip')->__('Wrong hour'));
		}
	}
}
?>
