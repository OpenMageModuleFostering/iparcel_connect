<?php
/**
 * Shipment tracking control form
 *
 * @category   Iparcel
 * @package    Iparcel_Shipping
 * @author     Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Block_Adminhtml_Sales_Order_Shipment_View_Tracking extends Mage_Adminhtml_Block_Sales_Order_Shipment_View_Tracking
{
    /**
     * Get relevant path to template
     *
     * @return string
     */
    public function getTemplate()
    {
        if ($this->getShipment()->getOrder()->getShippingMethod(true)->getCarrierCode() == 'i-parcel') {
            return 'iparcel/sales/order/shipment/view/tracking.phtml';
        } else {
            return parent::getTemplate();
        }
    }
}
