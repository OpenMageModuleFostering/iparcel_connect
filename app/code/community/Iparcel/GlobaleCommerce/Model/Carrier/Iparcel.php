<?php
/**
 * i-parcel shipping method model
 *
 * @category    Iparcel
 * @package     Iparcel_GlobaleCommerce
 * @author      Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_GlobaleCommerce_Model_Carrier_Iparcel extends Iparcel_All_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'i-parcel-globalecommerce';
    protected $_carrier = 'i-parcel-globalecommerce';
    protected $_isFixed = true;

    /**
     * Do request to shipment
     *
     * @param Mage_Shipping_Model_Shipment_Request $request
     * @return Varien_Object
     */
    public function requestToShipment(Mage_Shipping_Model_Shipment_Request $request)
    {
        $shipping = $request->getOrderShipment();
        /* var $shipping Mage_Sales_Model_Order_Shipment */
        $tracking = $shipping->getAllTracks();
        if (empty($tracking)) {
            Mage::throwException('Invalid Request To Shipment Call');
        }
        $tracking = $tracking[0];
        /* var $tracking Mage_Sales_Model_Order_Shipment_Track */
        // prepare label PDF
        $pdf = new Zend_Pdf();
        $number = $tracking->getNumber();
        $pdfPage = $pdf->pages[] = new Zend_Pdf_Page(('162:75'));
        $barcodeFont = Zend_Pdf_Font::fontWithPath(
            Mage::getBaseDir('skin') . '/adminhtml/default/default/iparcel/font/code128.ttf'
        );
        $courier = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_COURIER);
        $pdfPage->setFont($courier, 10);
        $pdfPage->drawText($number, 15, 10);
        $pdfPage->setFont($barcodeFont, 40);
        $pdfPage->drawText($number, 15, 25);
        $info = array();
        $info[] = array(
            'label_content'         =>  $pdf->render(),
            'tracking_number'   =>  $number
        );
        // prepare response
        $response = new Varien_Object();
        $response->setTrackingNumer($number);
        $response->setInfo($info);
        return $response;
    }

    /**
     * Always returns true. This is only used for inserted orders.
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Shipping_Model_Rate_Result|bool
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        return true;
    }

    /**
     * Returns false to prevent the method from being visible to customers.
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return false;
    }
}
