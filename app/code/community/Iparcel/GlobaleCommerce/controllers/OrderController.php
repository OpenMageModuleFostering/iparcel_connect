<?php
/**
 * External API order controller
 *
 * @category    Iparcel
 * @package     Iparcel_GlobaleCommerce
 * @author      Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_GlobaleCommerce_OrderController extends Mage_Core_Controller_Front_Action
{
    /**
     * Checking if external sales API is enabled
     *
     * @return string
     */
    protected function _isEnabled()
    {
        return Mage::getStoreConfigFlag('external_api/sales/enabled');
    }

    /**
     * Checking if request is allowed
     *
     * @return Mage_Core_Controller_Request_Http
     */
    protected function _checkRequest()
    {
        $request = $this->getRequest();

        //var $request Mage_Core_Controller_Request_Http
        // checking if External Sales API is enabled
        if (!$this->_isEnabled()) {
            Mage::throwException('External Sales API is disabled');
        }
        // checking if it is POST request
        if ($request->getMethod() != 'POST') {
            Mage::throwException('Wrong HTTP request');
        }
        // checking if POST GUID key is correct
        if (strcasecmp($request->getPost('key'), Mage::helper('ipglobalecommerce')->getGuid())) {
            Mage::throwException('Wrong GUID key');
        }
        // checking if request IP is allowed
        if (!Mage::helper('ipglobalecommerce/api_external')->isAllowed()) {
            Mage::throwException('Access Forbidden');
        }
        return $request;
    }

    /**
     * Adding new order
     */
    public function AddAction()
    {
        // register global flag
        Mage::register('isExternalSale', true);
        try {
            $request = $this->_checkRequest();
            $orders = $request->getPost('orders');

            // terminate if there's no orders in request
            if (count($orders) == 0) {
                Mage::throwException('There are no orders in your request');
            }

            $tax = $request->getPost('tax');
            $tax = is_string($tax) ? (float)str_replace(",", ".", $tax) : $tax;
            $shipping = $request->getPost('shipping');
            $shipping = is_string($shipping) ? (float)str_replace(",", ".", $shipping) : $shipping;
            $currency = $request->getPost('currency');
            $tracking = $request->getPost('tracking');

            /** @var Iparcel_GlobaleCommerce_Model_Api_External_Sales_Order $model */
            $model = Mage::getModel('ipglobalecommerce/api_external_sales_order');

            // Make sure that an order doesn't already exist for this request
            $model->loadByTrackingNumber($tracking['number']);
            if ($model->getId()) {
                echo $model->getOrderIncrementId();
                return true;
            }

            $user = $request->getPost('user');
            // checking if user's email is specified
            if (!isset($user['email'])) {
                Mage::throwException('There is no customer email address in your request');
            }
            $model->setCustomer($user, Mage::app()->getWebsite()->getId());

            // If this is a new address, or the user doesn't have a default address
            if ($user['address']['new'] == 1 || $model->getCustomerHasDefaultBilling() == false) {
                $model->setCustomerBillingAddress($user['address']);
            } else {
                $model->setDefaultCustomerBillingAddress();
            }

            // If no ['shipping_address'] is given, reuse the ['address']
            if (array_key_exists('shipping_address', $user) && is_array($user['shipping_address'])) {
                $shippingAddress = $user['shipping_address'];
            } else {
                $shippingAddress = $user['address'];
            }

            // If this is a new address, or the user doesn't have a default address
            if ($shippingAddress['new'] == 1 || $model->getCustomerHasDefaultShipping() == false) {
                $model->setCustomerShippingAddress($shippingAddress);
            } else {
                $model->setDefaultCustomerShippingAddress();
            }

            // Convert ['options'] to id => value_id
            $orders = $this->_convertOptions($orders);

            // Check the order to see if there are products with Custom Options
            // If so, alter the order to reflect the Custom Options correctly
            if ($this->_orderHasCustomOptions($orders)) {
                $orders = $this->_addCustomOptionsToOrder($orders);
            }

            $model->setOrderData($orders);
            $model->setShippingCosts($shipping);
            $model->setTax($tax);
            // if tracking specified set tracking number
            if ($tracking !== null) {
                $model->setTrackingNumber($tracking['number']);
            }
            $model->setCurrency($currency);
            // if order created successfully
            $model->setStoreId(Mage::app()->getStore()->getId());
            if ($order = $model->createOrder()) {
                $invoice = $model->createInvoice();
                echo $order->getIncrementId();
                $model->setOrderIncrementId($order->getIncrementId());
                $model->save();
            }
        } catch (Mage_Core_Exception $e) {
            /** @var Iparcel_All_Model_Log $log */
            $log = Mage::getModel('iparcel/log');
            $log->setRequest($this->_checkRequest())
                ->setResponse($e->getMessage())
                ->setController('Add Order')
                ->save();

            $this->getResponse()->setHeader('Content-Type', '', true)
                ->setHttpResponseCode(500)
                ->setBody($e->getMessage())
                ->sendResponse();
            return;
        }
    }

    /**
     * Canceling an order
     */
    public function CancelAction()
    {
        try {
            $request = $this->_checkRequest();
            $order = Mage::getModel('sales/order')->loadByIncrementId($request->getPost('order'));
            // var $order Mage_Sales_Model_Order
            if ($order->getId()) {
                $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
                echo 'Order #'.$request->getPost('order').' cancelled.';
            } else {
                Mage::throwException('Order no longer exists');
            }
        } catch (Mage_Core_Exception $e) {
            echo $e->getMessage();
        }
    }

    /**
     * Checks the Order for a product with Custom Options
     *
     * @param array $orders Array of orders from the request
     * @return bool true if the product has Custom Options
     */
    private function _orderHasCustomOptions($orders)
    {
        foreach($orders as $order) {
            $product = Mage::getModel('catalog/product')
                ->loadByAttribute('sku', $order['sku']);

            /**
             * If the SKU doesn't load a product, the SKU is built from a
             * product with custom options
             */
            if ($product == false) {
                return true;
            }
        }

        return false;
    }

    /**
     * Adds Custom Option information to the order data
     *
     * @param array $orders Array of orders from the request
     * @return array Updated $orders array
     */
    private function _addCustomOptionsToOrder($orders)
    {
        foreach($orders as &$order) {
            // Attempt to load the product by stripping out the Custom Options
            $splitSku = preg_split('/__/', $order['sku']);
            $sku = $splitSku[0];

            $product = Mage::getModel('catalog/product')
                ->loadByAttribute('sku', $sku);
            /**
             * If the SKU doesn't load a product, return the existing $orders
             * array, and attempt order creation
             */
            if ($product == false) {
                continue;
            }

            $order['sku'] = $sku;

            /**
             * Split the SKU options into an array of:
             * [optionID] => optionValueID
             */
            if (count($splitSku) > 1) {
                $optionsValues = preg_split('/_/', $splitSku[1]);
                foreach($optionsValues as $key => $optionValue) {
                    unset($optionsValues[$key]);
                    $split = preg_split('/-/', $optionValue);
                    $optionsValues[$split[0]] = $split[1];
                }

                $order['options'] = $optionsValues;
            }
        }

        return $orders;
    }

    /**
     * Converts ['options'] array into 'id' => 'value_id'
     *
     * @param array $orders Array of orders from the request
     * @return array Updated $ordes array
     */
    private function _convertOptions($orders)
    {
        foreach ($orders as &$order) {
            if (!array_key_exists('options', $order)) {
                continue;
            }

            $product = Mage::getModel('catalog/product')
                ->loadByAttribute('sku', $order['sku']);
            $options = $order['options'];

            if (is_null($product) || $product == false) {
                /**
                 * If this product has options, and has a generated SKU, the
                 * product won't load from the SKU. Instead, strip the options
                 * and load the product.
                 */
                $sku = preg_replace('/__.*/', '', $order['sku']);
                $product = Mage::getModel('catalog/product')
                    ->loadByAttribute('sku', $sku);
                $order['sku'] = $sku;
            }

            $product = $product->load($product->getId());
            $productOptions = $product->getOptions();

            foreach($options as $title => $value) {
                foreach($productOptions as $id => $productOption) {
                    if ($productOption->getTitle() == $title) {
                        /**
                         * If this is a text field, add the id => value to the
                         * $options array. Otherwise, add id => value_id.
                         */
                        if (in_array($productOption->getType(), array('field', 'area'))) {
                            $options[$id] = $value;
                            unset($options[$title]);
                        } else {
                            foreach($productOption->getValues() as $valueId => $valueObject) {
                                if ($valueObject->getTitle() == $value) {
                                    $options[$id] = $valueId;
                                    unset($options[$title]);
                                }
                            }
                        }
                    }
                }
            }

            $order['options'] = $options;
        }

        return $orders;
    }

}
