<?php
/**
 * Sales_Order observer class
 *
 * @category    Iparcel
 * @package     Iparcel_GlobaleCommerce
 * @author      Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_GlobaleCommerce_Model_Sales_Order_Observer
{
    /**
     * Initializing CPF
     *
     * @param Mage_Sales_Model_Order $order
     */
    protected function _initCpf($order)
    {
        $cpf = Mage::getModel('ipglobalecommerce/cpf_order')->loadByOrderId($order->getId());
        /* var $cpf Iparcel_GlobaleCommerce_Model_Cpf_Order */
        if ($cpf->getId()) {
            $order->setCpf($cpf->getValue());
        }
    }

    /**
     * Initializing Parcel
     *
     * @param Mage_Sales_Model_Order $order
     */
    protected function _initParcel($order)
    {
        $parcel = Mage::getModel('ipglobalecommerce/parcel')->loadByOrderId($order->getId());
        if ($parcel->getParcelId()) {
            $order->setParcel($parcel->getParcelId());
        }
    }

    /**
     * Searching for traching number in order comments
     *
     * @param Mage_Sales_Model_Order $order
     * @return Mage_Sales_Model_Order_Status_History
     */
    protected function _searchForTrackingNumber($order)
    {
        foreach ($order->getStatusHistoryCollection() as $status) {
            /* var $status Mage_Sales_Model_Order_Status_History */
            if (preg_match('/^i-parcel tracking number: ([0-9A-Z]+)$/', $status->getComment()) == 1) {
                return $status;
            }
        }
        return null;
    }

    /**
     * Creating CPF for an order
     *
     * @param Mage_Sales_Model_Order $order
     */
    protected function _createCpf($order)
    {
        if ($quote = $order->getQuote()) {
            /* var $quote Mage_Sales_Model_Quote */
            $cpf = Mage::getModel('ipglobalecommerce/cpf_order')->loadByOrderId($order->getId());
            /* var $cpf Iparcel_GlobaleCommerce_Model_Cpf_Order */
            // create CPF for order if quote has CPF
            if ($quote->getCpf()) {
                $cpf->setOrderId($order->getId());
                $cpf->setValue($quote->getCpf());
                $cpf->save();
            } elseif ($cpf->getId()) {
                $cpf->delete();
            }
        }
    }

    /**
     * Creating Parcel for an order
     *
     * @param Mage_Sales_Model_Order $order
     */
    protected function _createParcel($order, $parcel_id)
    {
        $parcel = Mage::getModel('ipglobalecommerce/parcel')
            ->setOrderId($order->getId())
            ->setParcelId($parcel_id);
        $parcel->save();
    }

    /**
     * Setting order prefix for an order
     *
     * @param Mage_Sales_Model_Order $order
     */
    protected function _setOrderPrefix($order)
    {
        // if prefix is specified and shipping carrier is i-parcel
        if (Mage::getStoreConfig('carriers/i-parcel/prefix') && $order->getShippingCarrier()->getCarrierCode() == 'i-parcel') {
            // and there's no prefix at the beginning
            if (strpos($order->getIncrementId(), Mage::getStoreConfig('carriers/i-parcel/prefix')) !== 0) {
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
    protected function _handleExternal($order)
    {
        // if external sale is registered and choosen order status is not STATE_COMPLETE
        if (Mage::registry('isExternalSale') && ($status=Mage::getStoreConfig('external_api/sales/order_status')) != Mage_Sales_Model_Order::STATE_COMPLETE) {
            // set new state
            $order->setState($status, $status);
        }
    }

    /**
     * sales_order_save_before event handler
     */
    public function before_save($observer)
    {
        $order = $observer->getOrder();
        $this->_setOrderPrefix($order);
        $this->_handleExternal($order);
    }

    /**
     * sales_order_save_after event handler
     */
    public function after_save($observer)
    {
        $order = $observer->getOrder();
        $this->_createCpf($order);
    }

    /**
     * sales_order_load_after event handler
     */
    public function after_load($observer)
    {
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
        $this->_createParcel($order, Mage::getSingleton('checkout/session')->getParcelId());
    }
}
