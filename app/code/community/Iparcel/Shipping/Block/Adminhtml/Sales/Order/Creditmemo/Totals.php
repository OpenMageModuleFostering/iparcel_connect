<?php
/**
 * Iparcel order creditmemo shipping tax&duty totals block
 *
 * @category    Iparcel
 * @package     Iparcel_Shipping
 * @author      Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Block_Adminhtml_Sales_Order_Creditmemo_Totals extends Mage_Adminhtml_Block_Sales_Order_Creditmemo_Totals
{
    /**
     * Initialize creditmemo totals array
     *
     * @return Iparcel_Shipping_Block_Adminhtml_Sales_Order_Creditmemo_Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        foreach (Mage::helper('shippingip/tax')->getTotal($this->getOrder()) as $total) {
            $this->addTotal($total, array('shipping','tax'));
        }
        return $this;
    }
}
