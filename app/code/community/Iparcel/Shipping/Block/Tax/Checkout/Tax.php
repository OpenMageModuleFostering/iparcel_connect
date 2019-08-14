<?php
class Iparcel_Shipping_Block_Tax_Checkout_Tax extends Mage_Tax_Block_Checkout_Tax
{
    protected function _toHtml()
    {
        /* var $shippingAddress Mage_Sales_Model_Quote_Address */
        $shippingAddress = $this->getQuote()->getShippingAddress();
        if ($shippingAddress->getId()) {
            $shippingMethod = explode('_', $shippingAddress->getShippingMethod());
            $carrier = $shippingMethod[0];
            $carrier_flag = ($carrier == 'i-parcel');
            $domestic_flag = ($shippingAddress->getCountryId() == Mage::getStoreConfig('general/store_information/merchant_country'));
        } else {
            $carrier_flag = false;
            $domestic_flag = false;
        }

        $iparcel_tax_intercept = Mage::getStoreConfigFlag('iparcel/tax/mode');

        if (!$iparcel_tax_intercept || !$carrier_flag || $domestic_flag) {
            return parent::_toHtml();
        }
    }
}
