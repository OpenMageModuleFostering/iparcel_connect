<?php
/**
 * External API order controller
 *
 * @category		Iparcel
 * @package			Iparcel_Shipping
 * @author			Patryk Grudniewski <patryk.grudniewski@sabiosystem.com
 */
class Iparcel_Shipping_OrderController extends Mage_Core_Controller_Front_Action {
	/**
	 * Checking if external sales API is enabled
	 *
	 * @return string
	 */
	protected function _isEnabled(){
		return Mage::getStoreConfigFlag('external_api/sales/enabled');
	}

	/**
	 * Checking if request is allowed
	 *
	 * @return Mage_Core_Controller_Request_Http
	 */
	protected function _checkRequest(){
		$request = $this->getRequest();
		//var $request Mage_Core_Controller_Request_Http
		// checking if External Sales API is enabled
		if(!$this->_isEnabled()){
			Mage::throwException('External Sales API is disabled');
		}
		// checking if it is POST request
		if ($request->getMethod() != 'POST'){
			Mage::throwException('Wrong HTTP request');
		}
		// checking if POST GUID key is correct
		if (strcasecmp($request->getPost('key'), Mage::helper('shippingip')->getGuid())){
			Mage::throwException('Wrong GUID key');
		}
		// checking if request IP is allowed
		if (!Mage::helper('shippingip/api_external')->isAllowed()){
			Mage::throwException('Access Forbidden');
		}
		return $request;
	}

	/**
	 * Adding new order
	 */
	public function AddAction(){
		// register global flag
		Mage::register('isExternalSale',true);
		try{
			$request = $this->_checkRequest();
			$orders = $request->getPost('orders');
			$tax = $request->getPost('tax');
			$tax = is_string($tax) ? (float)str_replace(",",".",$tax) : $tax;
			$shipping = $request->getPost('shipping');
			$shipping = is_string($shipping) ? (float)str_replace(",",".",$shipping) : $shipping;
			$currency = $request->getPost('currency');
			$tracking = $request->getPost('tracking');
			$model = Mage::getModel('shippingip/api_external_sales_order');
			// var $model Iparcel_Shipping_Model_Api_External_Sales_Order
			// terminate if there's no orders in request
			if (count($orders) == 0){
				Mage::throwException('There are no orders in your request');
			}
			$user = $request->getPost('user');
			// checking if user's email is specified
			if (!isset($user['email'])){
				Mage::throwException('There is no customer email address in your request');
			}
			$model->setCustomer($user, Mage::app()->getWebsite()->getId());
			if ($user['address']['new'] == 1){
				$model->setCustomerBillingAddress($user['address']);
			}
			else{
				$model->setDefaultCustomerBillingAddress();
			}
			if ($user['shipping_address']['new'] == 1){
				$model->setCustomerShippingAddress($user['shipping_address']);
			}else{
				$model->setDefaultCustomerShippingAddress();
			}
			$model->setOrderData($orders);
			$model->setShippingCosts($shipping);
			$model->setTax($tax);
			// if tracking specified set tracking number
			if ($tracking !== NULL){
				$model->setTrackingNumber($tracking['number']);
			}
			$model->setCurrency($currency);
			// if order created successfully
			$model->setStoreId(Mage::app()->getStore()->getId());
			if ($order = $model->createOrder()){
				$invoice = $model->createInvoice();
				echo $order->getIncrementId();
			}
		}catch (Mage_Core_Exception $e){
			echo $e->getMessage();
		}
	}

	/**
	 * Canceling an order
	 */
	public function CancelAction(){
		try{
			$request = $this->_checkRequest();
			$order = Mage::getModel('sales/order')->loadByIncrementId($request->getPost('order'));
			// var $order Mage_Sales_Model_Order
			if ($order->getId()){
				$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
				echo 'Order #'.$request->getPost('order').' cancelled.';
			}else{
				Mage::throwException('Order no longer exists');
			}
		}catch(Mage_Core_Exception $e){
			echo $e->getMessage();
		}
	}
}
