<?php
/**
 * i-parcel External Sales API processing model
 *
 * @category    Iparcel
 * @package     Iparcel_Shipping
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_Shipping_Model_Api_External_Sales_Order extends Mage_Core_Model_Abstract
{
    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('shippingip/api_order');
    }

    /**
     * Initialize adminhtml quote session
     *
     * @return Iparcel_Shipping_Model_Api_External_Sales_Order
     */
    protected function _initSession()
    {
        $_orderData = $this->getOrderData();
        /* var $_orderData array */
        $data = $_orderData['session'];
        /* var $data array */
        if (!$data) {
            Mage::throwException('Session data not specified');
        }
        if (empty($data['customer_id'])) {
            Mage::throwException('Customer ID not specified');
        }
        Mage::getSingleton('adminhtml/session_quote')->setCustomerId((int) $data['customer_id']);
        return $this;
    }

    /**
     * Processing quote
     *
     * @return Iparcel_Shipping_Model_Api_External_Sales_Order
     */
    protected function _processQuote()
    {
        $data = $this->getOrderData();
        /* var $data array */
        if (empty($data['order'])) {
            Mage::throwException('Order Data not specified');
        }
        if (empty($data['add_products'])) {
            Mage::throwException('No products specified');
        }
        $_orderCreate = Mage::getSingleton('adminhtml/sales_order_create');
        /* var $_orderCreate Mage_Adminhtml_Model_Sales_Order_Create */
        $_orderCreate->importPostData($data['order']);
        $_orderCreate->getBillingAddress();
        $_orderCreate->setShippingAsBilling(true);
        $_orderCreate->addProducts($data['add_products']);
        $_orderCreate->getQuote()->getPayment()->addData($data['payment']);
        $_orderCreate->setPaymentData($data['payment']);
        $_orderCreate->initRuleData()->saveQuote();

        return $this;
    }

    /**
     * Customer setter
     *
     * @param array $user, int $websiteId
     * @return Iparcel_Shipping_Model_Api_External_Sales_Order
     */
    public function setCustomer($user, $websiteId)
    {
        $customer = Mage::getModel('customer/customer');
        /* var $customer Mage_Customer_Model_Customer */
        if ($websiteId) {
            $customer->setWebsiteId($websiteId);
        }
        $customer->loadByEmail($user['email']);
        // if customer exists set existing
        if ($customer->getId()) {
            parent::setCustomer($customer);
        } else {
            $customer->setEmail($user['email']);
            $customer->setFirstname($user['firstname']);
            $customer->setLastname($user['lastname']);
            $customer->setPassword($user['password']);
            $customer->setConfirmation(null);
            $customer->save();
            // else create new one
            parent::setCustomer($customer);
        }
        // set customer email and return
        $this->setCustomerEmail($user['email']);
        return $this;
    }

    /**
     * Customer Billing Address setter
     *
     * @param array $_address
     * @return Iparcel_Shipping_Model_Api_External_Sales_Order
     */
    public function setCustomerBillingAddress($_address)
    {
        $address = Mage::getModel('customer/address');
        /* var $address Mage_Customer_Model_Address */
        $customer = $this->getCustomer();
        /* var $customer Mage_Customer_Model_Customer */
        // throw exception if customer is not specified
        if (!$customer) {
            Mage::throwException('Customer not specified');
        }
        $region = Mage::getModel('directory/region')->loadByCode($_address['region_id'], $_address['country_id']);
        $address->setCustomerId($customer->getId());
        $address->setFirstname($_address['firstname']);
        $address->setLastname($_address['lastname']);
        $address->setCountryId($_address['country_id']);
        $address->setStreet($_address['street']);
        $address->setPostcode($_address['postcode']);
        $address->setCity($_address['city']);
        $address->setTelephone(isset($_address['telephone']) ? $_address['telephone'] : '');
        $address->setRegion(isset($_address['region_id']) ? $_address['region_id'] : '');
        $address->setRegionId($region->getId());
        $address->save();
        return parent::setCustomerBillingAddress($address);
    }

    /**
     * Setting default billing address for customer
     *
     * @return Iparcel_Shipping_Model_Api_External_Sales_Order
     */
    public function setDefaultCustomerBillingAddress()
    {
        $customer = $this->getCustomer();
        /* var $customer Mage_Customer_Model_Customer */
        if (!$customer) {
            Mage::throwException('Customer is not specified');
        }

    /* if no default billing do nothing */
        if (!$customer->getDefaultBilling()) {
            return $this;
        }

        $address = $customer->getDefaultBillingAddress();
        /* var $address Mage_Customer_Model_Address */
        return parent::setCustomerBillingAddress($address);
    }

    /**
     * Helper function to determine if the customer has a default Billing Address
     *
     * @return boolean True if a default billing address is set for the customer
     */
    public function getCustomerHasDefaultBilling()
    {
        $customer = $this->getCustomer();
        return (bool)$customer->getDefaultBilling();
    }

    /**
     * Customer Shipping Address setter
     *
     * @param array $_address
     * @return Iparcel_Shipping_Model_Api_External_Sales_Order
     */
    public function setCustomerShippingAddress($_address)
    {
        $address = Mage::getModel('customer/address');
        /* var $address Mage_Customer_Model_Address */
        $customer = $this->getCustomer();
        /* var $customer Mage_Customer_Model_Customer */
        // throw exception if customer is not specified
        if (!$customer) {
            Mage::throwException('Customer not specified');
        }
        $region = Mage::getModel('directory/region')->loadByCode($_address['region_id'], $_address['country_id']);
        $address->setCustomerId($customer->getId());
        $address->setFirstname($_address['firstname']);
        $address->setLastname($_address['lastname']);
        $address->setCountryId($_address['country_id']);
        $address->setStreet($_address['street']);
        $address->setPostcode($_address['postcode']);
        $address->setCity($_address['city']);
        $address->setTelephone(isset($_address['telephone']) ? $_address['telephone'] : '');
        $address->setRegion(isset($_address['region_id']) ? $_address['region_id'] : '');
        $address->setRegionId($region->getId());
        $address->save();
        return parent::setCustomerShippingAddress($address);
    }

    /**
     * Setting default shipping address for customer
     *
     * @return Iparcel_Shipping_Model_Api_External_Sales_Order
     */
    public function setDefaultCustomerShippingAddress()
    {
        $customer = $this->getCustomer();
        /* var $customer Mage_Customer_Model_Customer */
        if (!$customer) {
            Mage::throwException('Customer is not specified');
        }

        /* if no default shipping do nothing */
        if (!$customer->getDefaultShippingAddress()) {
            return $this;
        }

        $address = $customer->getDefaultShippingAddress();
        /* var $address Mage_Customer_Model_Address */
        return parent::setCustomerShippingAddress($address);
    }

    /**
     * Helper function to determine if the customer has a default Shipping Address
     *
     * @return boolean True if a default shipping address is set for the customer
     */
    public function getCustomerHasDefaultShipping()
    {
        $customer = $this->getCustomer();
        return (bool)$customer->getDefaultShipping();
    }

    /** Order data setter
     *
     * @param array $data
     * @return Iparcel_Shipping_Model_Api_External_Sales_Order
     */
    public function setOrderData($data)
    {
        $_products = array();
        $_options = new Varien_Data_Collection();
        foreach ($data as $product) {
            $_product = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToFilter('sku', $product['sku'])
                ->addAttributeToSelect('*')
                ->getFirstItem();
            /* var $_product Mage_Catalog_Model_Product */
            $_product->load($_product->getId());
            $_products[$_product->getId()] = array(
                'qty' => $product['qty'],
                'price' => is_string($product['price']) ? (float)str_replace(",", ".", $product['price']) : $product['price']
            );

            if (array_key_exists('options', $product)) {
                foreach ($product['options'] as $code => $value) {
                    $_options->addItem(new Varien_Object(
                        array(
                            'product' => $_product,
                            'code' => $code,
                            'value' => $value
                        )
                    ));
                }
                $_products[$_product->getId()]['options'] = $product['options'];
            }
        }
        // set products and options
        $this->setProducts($_products);
        $this->setOptions($_options);

        $_customerBillingAddress = $this->getCustomerBillingAddress();
        /* var $_customerBillingAddress Mage_Customer_Model_Address */
        $_customerShippingAddress = $this->getCustomerShippingAddress() ?: $this->getCustomerBillingAddress();
        /* var $_customerShippingAddress Mage_Customer_Model_Address */
        $_customer = $this->getCustomer();
        /* var $_customer Mage_Customer_Model_Customer */
        $_orderData = array(
            'session' => array(
                'customer_id' => $_customer->getId(),
                'store_id' => $this->getStoreId()
            ),
            'payment' => array(
                'method' => 'iparcel'
            ),
            'add_products' => $_products,
            'order' => array(
                'currency' => 'USD',
                'account' => array(
                    'group_id' => '1',
                    'email' => $_customer->getEmail()
                ),
                'billing_address' => array(
                    'customer_address_id' => $_customerBillingAddress->getId(),
                    'prefix' => '',
                    'firstname' => $_customerBillingAddress->getFirstname(),
                    'middlename' => '',
                    'lastname' => $_customerBillingAddress->getLastname(),
                    'suffix' => '',
                    'company' => '',
                    'street' => $_customerBillingAddress->getStreet(),
                    'city' => $_customerBillingAddress->getCity(),
                    'country_id' => $_customerBillingAddress->getCountryId(),
                    'region' => $_customerBillingAddress->getRegion(),
                    'region_id' => $_customerBillingAddress->getRegionId(),
                    'postcode' => $_customerBillingAddress->getPostcode(),
                    'telephone' => $_customerBillingAddress->getTelephone(),
                    'fax' => ''
                ),
                'shipping_address' => array(
                    'customer_address_id' => $_customerShippingAddress->getId(),
                    'prefix' => '',
                    'firstname' => $_customerShippingAddress->getFirstname(),
                    'middlename' => '',
                    'lastname' => $_customerShippingAddress->getLastname(),
                    'suffix' => '',
                    'company' => '',
                    'street' => $_customerShippingAddress->getStreet(),
                    'city' => $_customerShippingAddress->getCity(),
                    'country_id' => $_customerShippingAddress->getCountryId(),
                    'region' => $_customerShippingAddress->getRegion(),
                    'region_id' => $_customerShippingAddress->getRegionId(),
                    'postcode' => $_customerShippingAddress->getPostcode(),
                    'telephone' => $_customerShippingAddress->getTelephone(),
                    'fax' => ''
                ),
                'shipping_method' => 'i-parcel_auto',
                'comment' => array(
                    'customer_note' => 'This order has been programmatically created via I-Parcel extension'
                ),
                'send_confirmation' => '0'
            ),
        );
        return parent::setOrderData($_orderData);
    }

    /**
     * Creating order method
     *
     * @return Mage_Sales_Model_Order
     */
    public function createOrder()
    {
        $orderData = $this->getOrderData();
        /* var $orderData array */
        if (!$orderData) {
            Mage::throwException('Order Data is not specified');
        }

        $tax = $this->getTax() ?: 0;
        $shippingCosts = $this->getShippingCosts();
        $this->_initSession();
        $this->_processQuote();

        $_orderCreator = Mage::getSingleton('adminhtml/sales_order_create');
        /* var $_orderCreator Mage_Adminhtml_Sales_Order_Create */

        $subtotal = 0;

        // processing order's items, setting prices, totals etc
        foreach ($this->getProducts() as $id => $productData) {
            $_product = Mage::getModel('catalog/product')->load($id);
            /* var $_product Mage_Catalog_Model_Product */
            $items = Mage::getSingleton('adminhtml/sales_order_create')->getQuote()->getItemsCollection();
            $item = false;
            foreach($items as $currentItem) {
                if ($currentItem->getProduct()->getId() == $id) {
                    $item = $currentItem;
                    break;
                }
            }
            if (!$item) {
                continue;
            }
            foreach ($this->getOptions() as $option) {
                if ($option['product']->getId() == $_product->getId()) {
                    $item->addOption($option);
                }
            }

            $price = empty($productData['price']) ? $_product->getFinalPrice() : $productData['price'];
            $qty = $productData['qty'];
            $item->setCustomPrice($price);
            $item->setOriginalCustomPrice($price);
            $rowtotal = (float)$qty * (float)$price;
            $subtotal += $rowtotal;
            $item->setRowTotal($rowtotal);
        }

        // calc grand total
        $grandtotal = $subtotal+$tax+$shippingCosts;

        // set currency
        if (!$this->getCurrency()) {
            Mage::throwException('Currency is not specified');
        }
        $currency = $this->getCurrency();

        // generate shipping rate
        $ship = Mage::getModel('shipping/rate_result_method');
        /* var $ship Mage_Shipping_Model_Rate_Result_Method */
        $ship->setCarrier('i-parcel');
        $ship->setCarrierTitle('i-parcel');
        $ship->setMethod('auto');
        $ship->setMethodTitle('Auto');
        $ship->setPrice($shippingCosts);
        $ship->setCost($shippingCosts);
        $ship->setMethodDescription(Mage::getStoreConfig('carriers/i-parcel/whitelabelship').': '.Mage::getStoreConfig('carriers/i-parcel/backend_name'));

        // import order's post data and shipping rate
        $_orderCreator->importPostData($orderData['order'])
            ->getQuote()
            ->getShippingAddress()
            ->addShippingRate(Mage::getModel('sales/quote_address_rate')->importShippingRate($ship));

        try {
            // create order
            $_order = $_orderCreator->createOrder();
            /* var $_order Mage_Sales_Model_Order */
        } catch (Exception $e) {
            $errors = array();

            $messages = Mage::getSingleton('adminhtml/session_quote')->getMessages();
            /* var $messages Mage_Core_Model_Message_Collection */
            foreach ($messages->getErrors() as $error) {
                /* var $error Mage_Core_Model_Message_Error */
                $errors[] = $error->getText();
                $messages->deleteMessageByIdentifier($error->getIdentifier());
            }
            $errors = implode("\n", $errors);

            $message = $e->getMessage();
            $message = trim($message);
            if ($message != '') {
                $errors .= "\n{$message}";
            }

            Mage::throwException($errors);
        }

        $_order->setShippingDescription($ship->getMethodDescription());
        $_order->setBaseShippingAmount($ship->getPrice());
        $_order->setShippingAmount($ship->getPrice());
        $_order->setGlobalCurrencyCode($currency);
        $_order->setBaseCurrencyCode($currency);
        $_order->setStoreCurrencyCode($currency);
        $_order->setOrderCurrencyCode($currency);
        $_order->setTaxAmount($tax);
        $_order->setSubtotal($subtotal);
        $_order->setGrandTotal($grandtotal);
        $_order->setBaseGrandTotal($grandtotal-$tax);
        $_order->save();
        $_order->sendNewOrderEmail();

        Mage::getSingleton('adminhtml/session_quote')->clear();
        Mage::unregister('rule_data');

        $this->setOrder($_order);
        return $_order;
    }

    /**
     * Creating invoice for the order
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function createInvoice()
    {
        $order = $this->getOrder();
        /* var $order Mage_Sales_Model_Order */
        if (!$order->canInvoice()) {
            Mage::throwException(Mage::helper('core')->__('Cannot create an invoice.'));
        }
        $invoice = Mage::getModel('sales/service_order', $order)->prepareInvoice();
        /* var $invoice = Mage_Sales_Model_Order_Invoice */
        if (!$invoice->getTotalQty()) {
            Mage::throwException(Mage::helper('core')->__('Cannot create invoice without products.'));
        }
        $invoice->setRequestCaptureCase(Mage_Sales_Model_Order_Invoice::CAPTURE_OFFLINE);
        $invoice->register();
        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($invoice)
            ->addObject($invoice->getOrder());
        /* var $transactionSave Mage_Core_Model_Resource_Transaction */
        $transactionSave->save();
        $invoice->sendEmail();
        $this->setInvoice($invoice);
        return $invoice;
    }

    /**
     * Setter for tracking number
     *
     * @param string $number
     * @return Iparcel_Shipping_Model_Api_External_Sales_Order
     */
    public function setTrackingNumber($number)
    {
        Mage::getSingleton('checkout/session')->setTrackingNumber($number);
        return parent::setTrackingNumber($number);
    }

    /**
     * Load an Order by Tracking Number
     *
     * @param string $trackingNumber
     * @return Iparcel_Shipping_Model_Api_External_Sales_Order
     */
    public function loadByTrackingNumber($trackingNumber)
    {
        $this->load($trackingNumber, 'tracking_number');
        return $this;
    }
}
