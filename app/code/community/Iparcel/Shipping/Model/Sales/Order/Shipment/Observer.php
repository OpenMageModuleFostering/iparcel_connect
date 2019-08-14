<?php
/**
 * Sales_Order_Shipment observer class
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author      Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_Sales_Order_Shipment_Observer
{
    /**
     * Getting tracking number from order's comments
     *
     * @param Mage_Sales_Model_Order $order
     * @return string
     */
    protected function _getTrackingNumber($order)
    {
        $trackingNumber = '';
        foreach ($order->getStatusHistoryCollection() as $status) {
            preg_replace_callback('/^i-parcel tracking number: ([0-9A-Z]+)$/', function ($matches) use (&$trackingNumber) {
                $trackingNumber = $matches[1];
            }, $status->getComment());
        }
        return $trackingNumber;
    }

    /**
     * Creating shipping label
     *
     * @param Mage_Sales_Model_Order_Shipment $shipment
     */
    protected function _createShippingLabel($shipment)
    {
        $request = Mage::getModel('shipping/shipment_request');
        /* var $request Mage_Shipping_Model_Shipment_Request */
        $request->setOrderShipment($shipment);
        $response = $shipment->getOrder()->getShippingCarrier()->requestToShipment($request);
        $info = $response->getInfo();
        $pdfContent = $info[0]['label_content'];

        // parse PDF content
        $outputPdf = new Zend_Pdf();
        $outputPdf = Zend_Pdf::parse($pdfContent);
        $shipment->setShippingLabel($outputPdf->render());
    }

    /**
     * Creating tracking
     *
     * @param Mage_Sales_Model_Order_Shipment $shipment
     */
    protected function _createTracking($shipment)
    {
        // do nothing if shipment has any tracks
        if (count($shipment->getAllTracks())) {
            return;
        }
        $trackingNumber = $this->_getTrackingNumber($shipment->getOrder());
        if ($trackingNumber != '') {
            $tracking = Mage::getModel('sales/order_shipment_track')
                ->setNumber($trackingNumber)
                ->setCarrierCode('i-parcel')
                ->setTitle('i-parcel');
            /* var $tracking Mage_Sales_Model_Order_Shipment_Track */
            $shipment->addTrack($tracking);
            $this->_createShippingLabel($shipment);
        }
    }

    /**
     * sales_order_shipment_save_before event handler
     */
    public function before_save($observer)
    {
        $shipment = $observer->getShipment();
        if ($shipment->getOrder()->getShippingCarrier() && $shipment->getOrder()->getShippingCarrier()->getCarrierCode() == 'i-parcel') {
            $this->_createTracking($shipment);
        }
    }
}
