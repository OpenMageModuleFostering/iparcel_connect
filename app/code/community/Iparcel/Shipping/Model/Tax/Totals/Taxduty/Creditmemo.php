<?php
/**
 * Shipping tax&duty tax for creditmemo total class
 *
 * @category	Iparcel
 * @package		Iparcel_Shipping
 * @author		Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_Tax_Totals_Taxduty_Creditmemo extends Mage_Sales_Model_Order_Creditmemo_Total_Abstract{
	/**
	 * Collect credit memo subtotal
	 *
	 * @param Mage_Sales_Model_Order_Creditmemo $creditmemo
	 * @return Iparcel_Shipping_Model_Tax_Totals_Taxduty_Creditmemo
	 */
	public function collect(Mage_Sales_Model_Order_Creditmemo $creditmemo){
		$taxduty = Mage::getModel('shippingip/tax_totals')->loadByOrderId($creditmemo->getOrder()->getId());
		/* var $taxduty Iparcel_Shipping_Model_Tax_Totals */
		if ($taxduty->getMode() == Iparcel_Shipping_Model_System_Config_Source_Tax_Mode::CUMULATIVELY){
			$amount = $taxduty->getTax()+$taxduty->getDuty();
			$creditmemo->setGrandTotal($creditmemo->getGrandTotal()+$amount);
			$creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal()+$amount);
		}
		return $this;
	}
}
?>
