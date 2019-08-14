<?php
/**
 * Sales_Order observer class
 *
 * @category	Iparcel
 * @package		Iparcel_Shipping
 * @author		Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_Sales_Order_Observer{
	/**
	 * Initializing CPF
	 *
	 * @param Mage_Sales_Model_Order $order
	 */
	protected function _initCpf($order){
		$cpf = Mage::getModel('shippingip/cpf_order')->loadByOrderId($order->getId());
		/* var $cpf Iparcel_Shippingip_Model_Cpf_Order */
		if($cpf->getId()){
			$order->setCpf($cpf->getValue());
		}
	}

	/**
	 * Initializing Parcel
	 *
	 * @param Mage_Sales_Model_Order $order
	 */
	protected function _initParcel($order){
		$parcel = Mage::getModel('shippingip/parcel')->loadByOrderId($order->getId());
		if ($parcel->getParcelId()){
			$order->setParcel($parcel->getParcelId());
		}
	}

	/**
	 * Searching for traching number in order comments
	 *
	 * @param Mage_Sales_Model_Order $order
	 * @return Mage_Sales_Model_Order_Status_History
	 */
	protected function _searchForTrackingNumber($order){
		foreach ($order->getStatusHistoryCollection() as $status){
			/* var $status Mage_Sales_Model_Order_Status_History */
			if (preg_match('/^i-parcel tracking number: ([0-9A-Z]+)$/', $status->getComment()) == 1){
				return $status;
			}
		}
		return NULL;
	}

	/**
	 * Creating shipment for an order
	 *
	 * @param Mage_Sales_Model_Order $order
	 */
	protected function _createShipment($order){
		if (!$order->getQuote()){
			return;
		}
		// if it's i-parcel shipping method
		if($order->getShippingCarrier() && $order->getShippingCarrier()->getCarrierCode() != 'i-parcel'){
			return;
		}
		if(!Mage::registry('isExternalSale') && Mage::getStoreConfigFlag('iparcel/config/submit_parcel')){
			/* var $submitParcel stdClass */
			$submitParcel = Mage::helper('shippingip/api')->submitParcel($order);
			if($submitParcel && isset($submitParcel->ParcelID)){
				Mage::getSingleton('checkout/session')->setParcelId($submitParcel->ParcelID);
			}
		}
		$tracking = Mage::getSingleton('checkout/session')->getTrackingNumber();
		if (!$tracking && isset($submitParcel)){
			$tracking = @$submitParcel->CarrierTrackingNumber;
		}
		/* var $tracking string */
		if ($tracking && !$this->_searchForTrackingNumber($order)){
			$order->addStatusHistoryComment('i-parcel tracking number: '.$tracking);
		}
		// if autoship is enabled and order can be shipped
		if (Mage::getStoreConfigFlag('carriers/i-parcel/autoship')){
			if ($order->canShip()){
				$converter = Mage::getModel('sales/convert_order');
				/* var $converter Mage_Sales_Model_Convert_Order */
				$shipment = $converter->toShipment($order);
				/* var $shipment Mage_Sales_Model_Order_Shipment */
				foreach ($order->getAllItems() as $orderItem){
					/* var $orderItem Mage_Sales_Model_Order_Item */
					// continue if it is virtual or there is no quantity to ship
					if (!$orderItem->getQtyToShip()) continue;
					if ($order->getIsVirtual()) continue;
					$item = $converter->itemToShipmentItem($orderItem);
					/* var $item Mage_Sales_Model_Order_Shipment_Item */
					$item->setQty($orderItem->getQtyToShip());
					$shipment->addItem($item);
				}
				$shipment->register();
				$shipment->getOrder()->setIsInProcess(true);
				$transactionSave = Mage::getModel('core/resource_transaction')
					->addObject($shipment)
					->addObject($order);
				/* var $transactionSave Mage_Core_Model_Resource_Transaction */
				$transactionSave->save();
				$shipment->save();
				$shipment->sendEmail();
			}
		}
	}

	/**
	 * Creating CPF for an order
	 *
	 * @param Mage_Sales_Model_Order $order
	 */
	protected function _createCpf($order){
		if ($quote = $order->getQuote()){
			/* var $quote Mage_Sales_Model_Quote */
			$cpf = Mage::getModel('shippingip/cpf_order')->loadByOrderId($order->getId());
			/* var $cpf Iparcel_Shipping_Model_Cpf_Order */
			// create CPF for order if quote has CPF
			if($quote->getCpf()){
				$cpf->setOrderId($order->getId());
				$cpf->setValue($quote->getCpf());
				$cpf->save();
			}elseif($cpf->getId()){
				$cpf->delete();
			}
		}
	}

	/**
	 * Creating Parcel for an order
	 *
	 * @param Mage_Sales_Model_Order $order
	 */
	protected function _createParcel($order, $parcel_id){
		$parcel = Mage::getModel('shippingip/parcel')
			->setOrderId($order->getId())
			->setParcelId($parcel_id);
		$parcel->save();
	}

	/**
	 * Saving Shipping Tax&Duty Totals
	 *
	 * @param Mage_Sales_Model_Order $order
	 */
	protected function _saveTaxDuty($order){
		$session = Mage::getSingleton('checkout/session');
		/* var $custSess Mage_Customer_Model_Session */
		$_taxduty = $session->getTaxDutyTotal();
		/* var $_taxduty array */
		$session->unsTaxDutyTotal();
		if ($_taxduty && $order->getShippingCarrier()->getCarrierCode() == 'i-parcel'){
			try{
				$taxduty = Mage::getModel('shippingip/tax_totals');
				$taxduty->setMode($_taxduty['mode']);
				$taxduty->setOrderId($order->getId());
				$taxduty->setTax($_taxduty['tax']);
				$taxduty->setDuty($_taxduty['duty']);
				$taxduty->save();
			} catch (Exception $e) {
				Mage::logException($e);
			}
		}
	}

	/**
	 * Processing Shipping Tax&Duty Totals
	 * Setting correct order grand_total
	 *
	 * @param Mage_Sales_Model_Order $order
	 */
	protected function _processTaxDuty($order){
		$_taxduty = Mage::getSingleton('checkout/session')->getTaxDutyTotal();
		/* var $_taxduty array */
		$order->setBaseGrandTotal($order->getBaseGrandTotal()+$_taxduty['tax']+$_taxduty['duty']);
		$order->setGrandTotal($order->getGrandTotal()+$_taxduty['tax']+$_taxduty['duty']);
	}

	/**
	 * Setting order prefix for an order
	 *
	 * @param Mage_Sales_Model_Order $order
	 */
	protected function _setOrderPrefix($order){
		// if prefix is specified and shipping carrier is i-parcel
		if(Mage::getStoreConfig('carriers/i-parcel/prefix') && $order->getShippingCarrier()->getCarrierCode() == 'i-parcel'){
			// and there's no prefix at the beginning
			if (strpos($order->getIncrementId(),Mage::getStoreConfig('carriers/i-parcel/prefix')) !== 0){
				// add prefix at the beginning of increment ID
				$order->setIncrementId(Mage::getStoreConfig('carriers/i-parcel/prefix').$order->getIncrementId());
			}
		}
	}

	/**
	 * Handling external sales API orders
	 * Setting choosen order status
	 *
	 * @param Mage_Sales_Model_Order $order
	 */
	protected function _handleExternal($order){
		// if external sale is registered and choosen order status is not STATE_COMPLETE
		if (Mage::registry('isExternalSale') && ($status=Mage::getStoreConfig('external_api/sales/order_status')) != Mage_Sales_Model_Order::STATE_COMPLETE){
			// set new state
			$order->setState($status, $status);
		}
	}

	/**
	 * sales_order_save_before event handler
	 */
	public function before_save($observer){
		$order = $observer->getOrder();
		$this->_setOrderPrefix($order);
		$this->_processTaxDuty($order);
		$this->_handleExternal($order);
	}

	/**
	 * sales_order_save_after event handler
	 */
	public function after_save($observer){
		$order = $observer->getOrder();
		$this->_saveTaxDuty($order);
		$this->_createCpf($order);
	}

	/**
	 * sales_order_load_after event handler
	 */
	public function after_load($observer){
		$order = $observer->getOrder();
		$this->_initCpf($order);
		$this->_initParcel($order);
	}

    /**
     * sales_order_place_after event handler
     * @param Varien_Event_Observer $observer
     */
    public function after_place(Varien_Event_Observer $observer)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $observer->getOrder();
        $this->_createShipment($order);
				$this->_createParcel($order,Mage::getSingleton('checkout/session')->getParcelId());
    }
}
