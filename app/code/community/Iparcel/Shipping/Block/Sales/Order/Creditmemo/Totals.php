<?php
/**
 * Block for i-parcel tax&duty creditmemo totals
 *
 * @category	 Iparcel
 * @package		 Iparcel_Shipping
 * @author		 Patryk Grudniewski
 */
class Iparcel_Shipping_Block_Sales_Order_Creditmemo_Totals extends Mage_Sales_Block_Order_Creditmemo_Totals{
	/**
	 * Initialize order totals array
	 *
	 * @return Iparcel_Shipping_Block_Sales_Order_Creditmemo_Totals
	 */
	protected function _initTotals(){
		parent::_initTotals();
		foreach (Mage::helper('shippingip/tax')->getTotal($this->getOrder()) as $total){
			$this->addTotal($total, array('shipping','tax'));
		}
		return $this;
	}
}
?>
