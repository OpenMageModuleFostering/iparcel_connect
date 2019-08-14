<?php
/**
 * i-parcel shipping tax&duty order totals block
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_Shipping_Block_Sales_Order_Totals extends Mage_Sales_Block_Order_Totals
{
    /**
     * Initialize order totals array
     *
     * @return Iparce-Shipping_Block_Sales_Order_Totals
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
