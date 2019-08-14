<?php
/**
 * Shipment view form
 *
 * @category   Iparcel
 * @package    Iparcel_Shipping
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_Shipping_Block_Adminhtml_Sales_Order_Shipment_View_Form extends Mage_Adminhtml_Block_Sales_Order_Shipment_View_Form
{
    /**
     * Get create label button html
     *
     * @return string
     */
    public function getCreateLabelButton()
    {
        if ($this->getShipment()->getOrder()->getShippingCarrier()->getCarrierCode() == "i-parcel") {
            return null;
        } else {
            return parent::getCreateLabelButton();
        }
    }
}