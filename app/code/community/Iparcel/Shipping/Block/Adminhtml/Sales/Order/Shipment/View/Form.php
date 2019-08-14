<?php
/**
 * Shipment view form
 *
 * @category   Iparcel
 * @package    Iparcel_Shipping
 * @author     Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Block_Adminhtml_Sales_Order_Shipment_View_Form extends Mage_Adminhtml_Block_Sales_Order_Shipment_View_Form{
	/**
	 * Get create label button html
	 *
	 * @return string
	 */
	public function getCreateLabelButton(){
		if ($this->getShipment()->getOrder()->getShippingCarrier()->getCarrierCode() == "i-parcel"){
			return NULL;
		}else{
			return parent::getCreateLabelButton();
		}	
	}
}
?>
