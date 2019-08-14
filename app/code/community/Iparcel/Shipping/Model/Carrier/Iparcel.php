<?php
/**
 * i-parcel shipping method model
 *
 * @category	Iparcel
 * @package		Iparcel_Shipping
 * @author		Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_Carrier_Iparcel extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {
	protected $_code = 'i-parcel';
	protected $_carrier = 'i-parcel';
	protected $_trackingUrl = 'https://tracking.i-parcel.com/secure/track.aspx?track='; 

	/**
	 * Check if carrier has shipping label option available
	 *
	 * @return bool
	 */
	public function isShippingLabelsAvailable(){
		return true;
	}

	/**
	 * Check if carrier has shipping tracking option available
	 *
	 * @return bool
	 */
	public function isTrackingAvailable(){
		return true;
	}

	/**
	 * Get info for track order page
	 *
	 * @param string $number
	 * @return Varien_Object
	 */
	public function getTrackingInfo($number){
		return new Varien_Object(array(
			'tracking' => $number,
			'carrier_title' => $this->_carrier,
			'url' => $this->_trackingUrl.$number
		));
	}

	/**
	 * Return container types of carrier
	 *
	 * @return array
	 */
	public function getContainerTypes(Varien_Object $params=NULL){
		return array('DEFAULT' => Mage::helper('shippingip')->__('Default box'));
	}

	/**
	 * Do request to shipment
	 *
	 * @param Mage_Shipping_Model_Shipment_Request $request
	 * @return Varien_Object
	 */
	public function requestToShipment(Mage_Shipping_Model_Shipment_Request $request){
		$shipping = $request->getOrderShipment();
		/* var $shipping Mage_Sales_Model_Order_Shipment */
		$tracking = $shipping->getAllTracks();
		if (empty($tracking)){
			Mage::throwException('Invalid Request To Shipment Call');
		}
		$tracking = $tracking[0];
		/* var $tracking Mage_Sales_Model_Order_Shipment_Track */

		// prepare label PDF
		$pdf = new Zend_Pdf();
		$number = $tracking->getNumber();
		$pdfPage = $pdf->pages[] = new Zend_Pdf_Page(('162:75'));
		$barcodeFont = Zend_Pdf_Font::fontWithPath(Mage::getBaseDir('media').'/font/code128.ttf');
		$courier = Zend_Pdf_Font::fontWithName(Zend_Pdf_Font::FONT_COURIER);
		$pdfPage->setFont($courier, 10);
		$pdfPage->drawText($number,15,10);
		$pdfPage->setFont($barcodeFont, 40);
		$pdfPage->drawText($number,15,25);

		$info = array();
		$info[] = array(
			'label_content'		=>	$pdf->render(),
			'tracking_number'	=>	$number
		);

		// prepare response
		$response = new Varien_Object();
		$response->setTrackingNumer($number);
		$response->setInfo($info);

		return $response;
	}

	/**
	 * Collect shipping rates for i-parcel shipping
     * refactor: add result check, add intermediate storage for parcel_id
	 *
	 * @param Mage_Shipping_Model_Rate_Request $request
	 * @return Mage_Shipping_Model_Rate_Result|bool
	 */
	public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        try {
            if (Mage::getStoreConfig('carriers/i-parcel/active')) {
                $iparcel = array();
                // array for tax&duty totals

                $result = Mage::getModel('shipping/rate_result');
                /*var $result Mage_Shipping_Model_Rate_Result */
                $businessSettings = Mage::helper('shippingip/api')->businessSettings();
                /* var $businessSettings stdClass */

                // collect rates only if the business settings model is 2 or 3
								if (/*true || */$businessSettings &&
                    isset($businessSettings->model) &&
                    in_array($businessSettings->model, array(2, 3))) {
                    $quote = Mage::helper('shippingip/api')->quote($request);
                    /* var $quote stdClass */
                    //if (!$quote) {
                        //return false;
                    //}

                    $serviceLevel = isset($quote->ServiceLevels) ?
                        $quote->ServiceLevels :
                        (object)array();

										//$serviceLevel = (object)array(
											//(object)array(
												//'ServiceLevelID'	=> 'test1',
												//'DutyCompanyCurrency'	=> '2.33',
												//'TaxCompanyCurrency' => '3.33',
												//'ShippingChargeCompanyCurrency' => '4.33'
											//),
											//(object)array(
												//'ServiceLevelID'	=> 'test2',
												//'DutyCompanyCurrency'	=> '3.33',
												//'TaxCompanyCurrency' => '4.33',
												//'ShippingChargeCompanyCurrency' => '5.33'
											//)
										//);

                    // Handling serviceLevels results and set up the shipping method
                    foreach ($serviceLevel as $ci) {
                        // setting up values
                        $servicename = @$ci->ServiceLevelID;

                        $duty = (float)@$ci->DutyCompanyCurrency;
                        $tax = (float)@$ci->TaxCompanyCurrency;
                        $shipping = (float)@$ci->ShippingChargeCompanyCurrency;

												$tax_flag = Mage::getStoreConfig('iparcel/tax/mode') == Iparcel_Shipping_Model_System_Config_Source_Tax_Mode::DISABLED
													|| $request->getDestCountryId() == $request->getCountryId();
                        // true if tax intercepting is disabled

                        $total = $tax_flag ? (float)($duty + $tax + $shipping) : (float)$shipping;
                        $shiplabel = Mage::getStoreConfig('carriers/i-parcel/whitelabelship');
                        $title = $tax_flag ?
                            Mage::helper('shippingip')
                                ->__('%s (Shipping Price: %s Duty: %s Tax: %s)',
                                    $shiplabel,
                                    $this->_formatPrice($shipping),
                                    $this->_formatPrice($duty),
                                    $this->_formatPrice($tax)) :
                            $shiplabel;

                        $method = Mage::getModel('shipping/rate_result_method');
                        $method->setCarrier('i-parcel');
                        $method->setCarrierTitle('i-parcel');
                        $method->setMethod($servicename);
                        $method->setMethodTitle($title);
                        $method->setPrice($total);
                        $method->setCost($total);
                        $method->setPriceOriginal($total);
                        $method->setPriceDuty($duty);
                        $method->setPriceTax($tax);
                        $method->setPriceInsurance($total);
                        $method->setPackageWeight($request->getPackageWeight());
                        $method->setMethodDescription(Mage::getStoreConfig('carriers/i-parcel/whitelabelship') . ': ' . Mage::getStoreConfig('carriers/i-parcel/backend_name'));

                        // append method to result
                        $result->append($method);

                        $iparcel['i-parcel_' . $servicename] = array(
                            'duty' => $duty,
                            'tax' => $tax
                        );
                    }
                }
                Mage::unregister('iparcel');
                Mage::register('iparcel', $iparcel);
                return $result;
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return false;
	}

    /**
     * @param float|int $price
     * @return string
     */
    protected function _formatPrice($price)
    {
        /** @var Mage_Core_Helper_Data $helper */
        $helper = Mage::helper('core');
        return $helper->formatPrice($price, false);
    }

	/**
	 * Get Allowed Shipping Methods
	 *
	 * @return array
	 */
	public function getAllowedMethods() {
		return array(
			'i-parcel' => $this->_carrier,
			'auto' => 'Auto'
		);
	}

}
