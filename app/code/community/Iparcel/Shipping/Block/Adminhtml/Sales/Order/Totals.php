<?php
/**
 * Iparcel order shipping tax&duty totals block
 *
 * @category    Iparcel
 * @package     Iparcel_Shipping
 * @author      Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Block_Adminhtml_Sales_Order_Totals extends Mage_Adminhtml_Block_Sales_Order_Totals
{
    /**
     * Initialize order totals array
     *
     * @return Iparcel_Shipping_Block_Adminhtml_Sales_Order_Totals
     */
    protected function _initTotals()
    {
        parent::_initTotals();
        $totalCollection = Mage::helper('shippingip/tax')->getTotal($this->getOrder());
        foreach ($totalCollection as $total) {
            $this->addTotal($total, array('shipping','tax'));
        }
        return $this;
    }
}
