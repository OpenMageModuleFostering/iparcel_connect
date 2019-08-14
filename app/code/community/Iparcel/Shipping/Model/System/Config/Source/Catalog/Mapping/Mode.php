<?php
/**
 * Source model for catalog_mapping/config/auto_upload config field
 *
 * @category	Iparcel
 * @package		Iparcel_Shipping
 * @author		Patryk Grudniewski
 */
class Iparcel_Shipping_Model_System_Config_Source_Catalog_Mapping_Mode{
	const DISABLED = "0";
	const ON_UPDATE = "1";
	const CRON = "2";

	/**
	 * Options list
	 *
	 * @return array
	 */
	public function toOptionArray(){
		return array(
			array('value' => self::DISABLED, 'label' => Mage::helper('shippingip')->__('Disabled')),
			array('value' => self::ON_UPDATE, 'label' => Mage::helper('shippingip')->__('On product save')),
			array('value' => self::CRON, 'label' => Mage::helper('shippingip')->__('Cron job'))
		);
	}
}
?>
