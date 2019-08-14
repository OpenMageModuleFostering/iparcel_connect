<?php
/**
 * Shipping tax tax for invoice total class
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author      Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_Tax_Totals_Tax_Invoice extends Mage_Sales_Model_Order_Invoice_Total_Abstract
{
    /**
     * Collect invoice subtotal
     *
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return Iparcel_Shipping_Model_Tax_Totals_Tax_Invoice
     */
    public function collect(Mage_Sales_Model_Order_Invoice $invoice)
    {
        $taxduty = Mage::getModel('shippingip/tax_totals')->loadByOrderId($invoice->getOrder()->getId());
        /* var $taxduty Iparcel_Shipping_Model_Tax_Totals */
        if ($taxduty->getMode() == Iparcel_Shipping_Model_System_Config_Source_Tax_Mode::SEPARATELY) {
            $amount = $taxduty->getTax();
            $invoice->setGrandTotal($invoice->getGrandTotal()+$amount);
            $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal()+$amount);
        }
        return $this;
    }
}
