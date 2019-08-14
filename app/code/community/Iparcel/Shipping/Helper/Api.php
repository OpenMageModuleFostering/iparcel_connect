<?php
/**
 * i-parcel sending API helper
 *
 * @category	Iparcel
 * @package		Iparcel_Shipping
 * @author		Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Helper_Api{
	protected $_submitCatalog = 'http://webservices.i-parcel.com/api/SubmitCatalog';
	protected $_quote = 'http://webservices.i-parcel.com/api/Quote';
	protected $_submitParcel = 'http://webservices.i-parcel.com/api/SubmitParcel';
	protected $_businessSettings = 'http://webservices.i-parcel.com/api/BusinessSettings';
	protected $_checkItems = 'http://webservices.i-parcel.com/api/CheckItems';

	/**
	 * Sending REST
	 *
	 * @param string $post, string $url, array $header
	 * @return string
	 */ 
	protected function _rest($post, $url, array $header){
		$curl = curl_init($url);

		$timeout = 15;
		if($timeout){
			curl_setopt($curl, CURLOPT_TIMEOUT,$timeout);
		}

		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		curl_setopt($curl, CURLOPT_POSTFIELDS, "$post");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

		$response = curl_exec($curl);

		curl_close($curl);

		return $response;
	}

	/**
	 * Sending REST XML
	 *
	 * @param SimpleXMLElement $xml, string $url
	 * @return string
	 */
	protected function _restXML($xml, $url){
		return $this->_rest($xml->asXml, $url, array('Content-Type: text/xml'));
	}

	/**
	 * Sending REST JSON
	 *
	 * @param string $json, string $url
	 * @return string
	 */
	protected function _restJSON($json, $url){
		return $this->_rest(json_encode($json),$url, array('Content-Type: text/json'));
	}
	
	/**
	 * Sending Submit Catalog request
	 *
	 * @param Varien_Data_Collection $productCollection
	 * @return int
	 */
	public function submitCatalog(Varien_Data_Collection $productCollection){
		//init log
		$log = Mage::getModel('shippingip/api_log');
		/* var $log Iparcel_Shipping_Model_Api_Log */	
		$log->setController('Submit Catalog');

		$hsCode = Mage::getModel('eav/entity_attribute')->load(Mage::getStoreConfig('catalog_mapping/attributes/hscodeus'));
		/* var $hsCode Mage_Eav_Model_Entity_Attribute */
		$shipAlone = Mage::getModel('eav/entity_attribute')->load(Mage::getStoreConfig('catalog_mapping/attributes/shipalone'));
		/* var $shipAlone Mage_Eav_Model_Entity_Attribute */

		$numberUploaded = 0;
		
		$json = array();
		$json['key'] = Mage::helper('shippingip')->getGuid();

		$skus = $json['SKUs'] = array();
		
		foreach ($productCollection as $product){
			$product=Mage::getModel('catalog/product')->load($product->getId());
			/* var $product Mage_Catalog_Model_Product */

			$sku = $product->getSku() ?: '';
			$name = $product->getName() ?: '';
			if (empty($sku) || empty($name)){
				continue;
			}

			$item = array();

			$item['SKU'] = $sku;
			$item['ProductName'] = $name;

			for ($i=0; $i<6; $i++){
				$_attribute = Mage::getModel('eav/entity_attribute')
					->load(Mage::getStoreConfig(sprintf('catalog_mapping/attributes/attribute%d',$i)));
				/* var $_attributeCollection[] Mage_Eav_Model_Entity_Attribute */
				// if attribute exists
				if ($_attribute !== null){
					// and has attribute_code
					if ($code = $_attribute->getAttributeCode()){
						// then productAttribute value is product's attribute_text (if exists) or product's data (if not)
						$productAttribute = strip_tags($product->getAttributeText($code) ?: $product->getData($code));
					}
				}
				$item[sprintf('Attribute%d',$i+1)] = substr($productAttribute,0,255);
			}

			$price = NULL;
			// if it's simple product and config is to get parent's price 
			if($product->getTypeId() == 'simple' && Mage::getStoreConfig('catalog_mapping/attributes/price') == Iparcel_Shipping_Model_System_Config_Source_Catalog_Mapping_Configurable_Price::CONFIGURABLE){
				// get parentIds
				$parentIds = Mage::getModel('catalog/product_type_grouped')->getParentIdsByChild($product->getId()) ?: Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($product->getId());
				// get price
				$price = $parentIds ? Mage::getModel('catalog/product')->load($parentIds[0])->getPrice() : $product->getPrice();
			}
			// if there's no price
			if(!$price){
				//get current product's price
				$price = $product->getPrice();
			}

			$item['CountryOfOrigin'] = $product->getCountryOfManufacture();
			$item['CurrentPrice'] = (float)$price;
			$item['Delete'] = $product->getIsDeleted() ? true : false;
			$item['HSCodeCA'] = '';

			if ($code = $hsCode->getAttributeCode()){
				$item['HSCodeUS'] = trim($product->getAttributeText($code)) ?: $product->getData($code);
			}else{
				$item['HSCodeUS'] = '';
			}

			$item['Height'] = (float)$product->getHeight();
			$item['Length'] = (float)$product->getLength();
			$item['ProductURL'] = $product->getUrlPath();
			$item['SKN'] = '';
			if ($code = $shipAlone->getAttributeCode()){
				$item['ShipAlone'] = $product->getAttributeText($code) == 'Yes' ? true : false;
			}
			$item['Width'] = (float)$product->getWidth();
			$item['Weight'] = (float)$product->getWeight();
			
			$numberUploaded++;

			$json['SKUs'][] = $item;
		}
		
		$log->setRequest(json_encode($json));

		if ($numberUploaded > 0){
			$response = $this->_restJSON($json, $this->_submitCatalog);

			$log->setResponse($response);
			$log->save();

			if (!preg_match('/.*Success.*/',$response)){
				$numberUploaded = -1;
			}
		}
		return $numberUploaded;
	}
	
	/**
	 * Sending Quote request
	 *
	 * @param Mage_Shipping_Model_Rate_Request $request
	 * @return stdClass
	 */
	public function quote(Mage_Shipping_Model_Rate_Request $request){
		// log init
		$log = Mage::getModel('shippingip/api_log');
		/* var $log Iparcel_Shipping_Model_Api_Log */	
		$log->setController('Quote');

		$quote = Mage::getModel('checkout/cart')->getQuote();
		/* var $quote Mage_Sales_Model_Quote */
		$shippingAddress = $quote->getShippingAddress();
		/* var $shippingAddress Mage_Sales_Model_Quote_Address */
		$billingAddress = $quote->getBillingAddress();
		/* var $billingAddress Mage_Sales_Model_Quote_Address */

		$json = array();
		$addressInfo = array();
		
		$billingStreet = $billingAddress->getStreet();

		$billing = array();
		$billing['City'] = $billingAddress->getCity();
		$billing['CountryCode'] = $billingAddress->getCountryId();
		$billing['Email'] = $quote->getCustomerEmail();
		$billing['FirstName'] = $billingAddress->getFirstname();
		$billing['LastName'] = $billingAddress->getLastname();
		$billing['Phone'] = $billingAddress->getTelephone();
		$billing['PostCode'] = $billingAddress->getPostcode();
		$billing['Region'] = $billingAddress->getRegion();
		for ($i=0; $i<count($billingStreet); $i++){
			$billing['Street'.($i+1)] = $billingStreet[$i];
		}

		$addressInfo['Billing'] = $billing;

		$shippingStreet = $shippingAddress->getStreet();

		$shipping = array();

		$shipping['City'] = $shippingAddress->getCity();
		$shipping['CountryCode'] = $shippingAddress->getCountryId();
		$shipping['Email'] = $quote->getCustomerEmail();
		$shipping['FirstName'] = $shippingAddress->getFirstname();
		$shipping['LastName'] = $shippingAddress->getLastname();
		$shipping['Phone'] = $shippingAddress->getTelephone();
		$shipping['PostCode'] = $shippingAddress->getPostcode();
		$shipping['Region'] = $shippingAddress->getRegion();
		for ($i=0; $i<count($shippingStreet); $i++){
			$shipping['Street'.($i+1)] = $billingStreet[$i];
		}

		$addressInfo['Shipping'] = $shipping;

		$addressInfo['ControlNumber'] = $quote->getCpf();

		$json['AddressInfo'] = $addressInfo;

		$json['CurrencyCode'] = $quote->getQuoteCurrencyCode();
		$json['DDP'] = true;
		
		$itemsList = array();

		foreach ($request->getAllItems() as $item){
			/* var $item Mage_Sales_Model_Quote_Item */

			$itemProduct = Mage::getModel('catalog/product')->load($item->getProductId());
			/* var $itemProduct Mage_Catalog_Model_Product */

			//get item price
			$itemPrice = (float)$item->getFinalPrice() ?: (float)$item->getPrice();
			// if not price and item has parent (is configurable)
			if (!$itemPrice && ($parent=$item->getParentItem())){
				// get parent price
				$itemPrice = (float)$parent->getFinalPrice() ?: (float)$parent->getPrice();
			}
			// if still not price
			if (!$itemPrice){
				// get product price
				$itemPrice = (float)$item->getProduct()->getPrice();
			}

			// if product isn't virtual and is configurable or downloadable
			if ($item["is_virtual"] == false && !in_array($itemProduct->getTypeId(),array('configurable','downloadable'))){
				// add line item node
				$lineItem = array();

				$lineItem['Quantity'] = $item->getTotalQty();
				$lineItem['SKU'] = $item->getSku();
				$lineItem['ShopperCurrency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
				$lineItem['ValueShopperCurrency'] = $itemPrice;
				$lineItem['ValueUSD'] = $itemPrice;

				$itemsList[] = $lineItem;
			}
		}

		$json['ItemDetailsList'] = $itemsList;

		// Get discounts
		$totals = $quote->getTotals();
		$discount = 0;
		if(isset($totals['discount']) && $totals['discount']->getValue()) 
			$discount = -1 * $totals['discount']->getValue();

		$json['OtherDiscount'] = $discount;
		$json['OtherDiscountCurrency'] = $quote->getQuoteCurrencyCode();
		$json['ParcelID'] = 0;
		$json['ServiceLevel'] = 115;
		$json['SessionID'] = '';
		$json['key'] = Mage::helper('shippingip')->getGuid();
		
		$log->setRequest(json_encode($json));

		$response = $this->_restJSON($json, $this->_quote);

		$log->setResponse($response);
		$log->save();
		
		return json_decode($response);
	}

	/**
	 * Sending Submit Parcel request
	 *
	 * $param Mage_Sales_Model_Order $order
	 * $return stdClass
	 */
	public function submitParcel(Mage_Sales_Model_Order $order){
		// init log
		$log = Mage::getModel('shippingip/api_log');
		/* var $log Iparcel_Shipping_Model_Api_Log */	
		$log->setController('Submit Parcel');

		$quote = $order->getQuote();
		/* var $quote Mage_Sales_Model_Quote */
		$shippingAddress = $quote->getShippingAddress();
		/* var $shippingAddress Mage_Sales_Model_Quote_Address */
		$billingAddress = $quote->getBillingAddress();
		/* var $billingAddress Mage_Sales_Model_Quote_Address */
		
		$json = array();

		$addressInfo = array();
		
		$billingStreet = $billingAddress->getStreet();

		$billing = array();

		$billing['City'] = $billingAddress->getCity();
		$billing['CountryCode'] = $billingAddress->getCountryId();
		$billing['Email'] = $quote->getCustomerEmail();
		$billing['FirstName'] = $billingAddress->getFirstname();
		$billing['LastName'] = $billingAddress->getLastname();
		$billing['Phone'] = $billingAddress->getTelephone();
		$billing['PostCode'] = $billingAddress->getPostcode();
		$billing['Region'] = $billingAddress->getRegion();
		$billing['Street1'] = $billingStreet[0];
		$billing['Street2'] = $billingStreet[1];

		$addressInfo['Billing'] = $billing;

		$shippingStreet = $shippingAddress->getStreet();

		$shipping = array();

		$shipping['City'] = $shippingAddress->getCity();
		$shipping['CountryCode'] = $shippingAddress->getCountryId();
		$shipping['Email'] = $quote->getCustomerEmail();
		$shipping['FirstName'] = $shippingAddress->getFirstname();
		$shipping['LastName'] = $shippingAddress->getLastname();
		$shipping['Phone'] = $shippingAddress->getTelephone();
		$shipping['PostCode'] = $shippingAddress->getPostcode();
		$shipping['Region'] = $shippingAddress->getRegion();
		$shipping['Street1'] = $shippingStreet[0];
		$shipping['Street2'] = $shippingStreet[1];
		
		$addressInfo['Shipping'] = $shipping;

		$addressInfo['ControlNumber'] = $order->getCpf();

		$json['AddressInfo'] = $addressInfo;

		$json['CurrencyCode'] = $quote->getQuoteCurrencyCode();
		$json['DDP'] = true;
		
		$itemsList = array();
		foreach ($order->getAllItems() as $item){
			/* var $item Mage_Sales_Model_Order_Item */
			$itemProduct = Mage::getModel('catalog/product')->load($item->getProductId());
			/* var $itemProduct Mage_Catalog_Model_Product */

			//get item price
			$itemPrice = (float)$item->getFinalPrice() ?: (float)$item->getPrice();
			// if not price and item has parent (is configurable)
			if (!$itemPrice && ($parent=$item->getParentItem())){
				// get parent price
				$itemPrice = (float)$parent->getFinalPrice() ?: (float)$parent->getPrice();
			}
			// if still not price
			if (!$itemPrice){
				// get product price
				$itemPrice = (float)$item->getProduct()->getPrice();
			}

			// if product isn't virtual and is configurable or downloadable
			if ($item["is_virtual"] == false && !in_array($itemProduct->getTypeId(),array('configurable','downloadable'))){
				// add line item node
				$lineItem = array();

				$lineItem['Quantity'] = $item->getQtyOrdered();
				$lineItem['SKU'] = $item->getSku();
				$lineItem['ShopperCurrency'] = Mage::app()->getStore()->getCurrentCurrencyCode();
				$lineItem['ValueShopperCurrency'] = $itemPrice;
				$lineItem['ValueUSD'] = $itemPrice;

				$itemsList[] = $lineItem;
			}
		}

		$json['ItemDetailsList'] = $itemsList;

		// if order_reference is set add it to request
		if (Mage::getStoreConfig('carriers/i-parcel/order_reference')){
			$json['OrderReference'] = $order->getIncrementId();
		}

		// Get discounts
		$totals = $quote->getTotals();
		$discount = 0;
		if(isset($totals['discount']) && $totals['discount']->getValue()) 
			$discount = -1 * $totals['discount']->getValue();

		// Get ServiceLevelID
		$method = $order->getShippingMethod();
		/* var $method string */
		$method = explode('_',$method);
		/* var $method array */
		array_shift($method);
		$serviceLevelId = implode('_',$method);
		/* var $serviceLevelId string */

		$json['OtherDiscount'] = $discount;
		$json['OtherDiscountCurrency'] = $quote->getQuoteCurrencyCode();
		$json['ParcelID'] = Mage::getSingleton('checkout/session')->getParcelId();
		$json['ServiceLevel'] = $serviceLevelId;
		$json['SessionID'] = '';
		$json['key'] = Mage::helper('shippingip')->getGuid();

		$log->setRequest(json_encode($json));
		
		$response = $this->_restJSON($json, $this->_submitParcel);

		$log->setResponse($response);
		$log->save();

		return json_decode($response);
	}

	/**
	 * Sending Business Settings request
	 *
	 * @return stdClass
	 */
	public function businessSettings(){
		// init log
		$log = Mage::getModel('shippingip/api_log');
		/* var $log Iparcel_Shipping_Model_Api_Log */	
		$log->setController('Business Settings');

		$json = Mage::helper('shippingip')->getGuid();

		$log->setRequest(json_encode($json));

		$response = $this->_restJSON($json, $this->_businessSettings);

		$log->setResponse($response);
		$log->save();

		return json_decode($response);
	}

	/**
	 * Sending Check Items request
	 *
	 * @params Varien_Data_Collection $collection
	 * @return int
	 */
	public function checkItems(Varien_Data_Collection $collection){
		//init log
		$log = Mage::getModel('shippingip/api_log');
		/* var $log Iparcel_Shipping_Model_Api_Log */	
		$log->setController('Check Items');

		$json = array();

		$json['SessionID'] = 'SESSION';
		$json['ItemDetailsList'] = array();

		foreach ($collection as $product){
			/* var $product Mage_Catalog_Model_Product */
			if (!$product->getSku()){
				continue;
			}
			$item = array();
			$item['SKU'] = $product->getSku();
			$item['Quantity'] = 1;
			$item['itemStyle'] = null;

			$json['ItemDetailsList'][] = $item;
		}

		$json['AddressInfo'] = array();

		$json['AddressInfo']['Billing'] = array();
		$json['AddressInfo']['Shipping'] = array();

		$json['AddressInfo']['Shipping']['CountryCode'] = "CA";
		$json['AddressInfo']['Shipping']['PostCode'] = "A1A1A1";

		$json['CurrencyCode'] = "CAN";
		$json['DDP'] = true;
		$json['Insurance'] = false;
		$json['ServiceLevel'] = 115;
		$json['key'] = Mage::helper('shippingip')->getGuid();

		$log->setRequest(json_encode($json));

		$response = $this->_restJSON($json, $this->_checkItems);

		$log->setResponse($response);
		$log->save();

		$response = json_decode($response);
		if ($response->ItemDetailsList){
			return count($response->ItemDetailsList);
		}else{
			return 0;
		}
	}

	/**
	 * Sending Sales Rule request
	 *
	 * @params Mage_SalesRule_Model_Resource_Rule_Collection
	 */
	public function salesRule(Mage_SalesRule_Model_Resource_Rule_Collection $_collection){
		$log = Mage::getModel('shippingip/api_log');
		/* var $log Iparcel_Shipping_Model_Api_Log */	
		$log->setController('Sales Rule');

		$json = array();
		$json['key'] = Mage::helper('shippingip')->getGuid();
		$json['ItemDetailsList'] = array();

		foreach ($_collection as $_rule){
			/* var $_rule Mage_SalesRule_Model_Rule */
			$item = array();
			$item['Name'] = $_rule->getName();
			$item['FormDate'] = $_rule->getFromDate();
			$item['ToDate'] = $_rule->getToDate();
			$item['IsActive'] = $_rule->getIsActive();
			$item['StopRulesProcessing'] = $_rule->getStopRulesProcessing();
			$item['SimpleAction'] = $_rule->getSimpleAction();
			$item['DiscountAmount'] = $_rule->getDiscountAmount();
			$item['DiscountQty'] = $_rule->getDiscountQty();
			$item['DiscountStep'] = $_rule->getDiscountStep();
			$item['SimpleFreeShipping'] = $_rule->getSimpleFreeShipping();
			$item['ApplyToShipping'] = $_rule->getApplyToShipping();
			$item['TimesUsed'] = $_rule->getTimesUsed();
			$item['CouponType'] = $_rule->getCouponType();

			$item['Conditions'] = unserialize($_rule->getConditionsSerialized());
			$item['Actions'] = unserialize($_rule->getActionsSerialized());

			$json['ItemDetailsList'][] = $item;
		}

		$log->setRequest(json_encode($json));

		$response = $this->_restJSON($json, null);

		$log->setResponse($response);
		$log->save();

		return json_decode($response);
	}
}
?>
