<?php
/**
 * Abstract class for i-parcel shipping tax&duty tax and totals
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
abstract class Iparcel_Shipping_Model_Tax_Totals_Abstract extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
    protected $_tax;
    protected $_duty;

    /**
     * Those constants have to be redefined in derived class
     */
    const XPATH_LABEL = null;
    const TOTAL_CODE = null;

    /**
     * Checking if current total is enabled
     *
     * @return bool
     */
    abstract protected function _isEnabled();

    /**
     * Adding amount to total
     */
    abstract protected function _addAmountToTotal();

    /**
     * Checking if shipping is domestic
     * @param Mage_Sales_Model_Quote_Address $address
     * @return bool
     */
    protected function _isDomestic($address)
    {
        return Mage::getStoreConfig('general/store_information/merchant_country') == $address->getCountryId();
    }

    /**
     * Getting mode od i-parcel shipping tax&duty
     *
     * @return string
     */
    protected function _getMode()
    {
        return Mage::getStoreConfig('iparcel/tax/mode');
    }

    /**
     * Getting label for current total
     *
     * @return string
     */
    protected function _getLabel()
    {
        return Mage::getStoreConfig($this::XPATH_LABEL);
    }

    /**
     * Setting total code in constructor
     */
    public function __construct()
    {
        $this->setCode($this::TOTAL_CODE);
    }

    /**
     * Collect totals process.
     *
     * @param Mage_Sales_Model_Quote_Address $address
     * @return Iparcel_Shipping_Model_Tax_Totals_Abstract
     */
    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        if ($this->_isEnabled() && !$this->_isDomestic($address)) {
            $quote = $address->getQuote();
            /* var $quote Mage_Sales_Model_Quote */
            // return if it's virtual quote
            if ($quote->isVirtual()) {
                return $this;
            }
            // we need shipping address
            if (($address->getAddressType() == 'billing')) {
                $address = $address->getQuote()->getShippingAddress();
            }
            $this->_setAddress($address);
            $_tax = 0;
            $_duty = 0;
            $_shipping = $address->getShippingMethod();
            /* var $_shipping string */
            $tax = Mage::registry('iparcel');
            /* var $tax array */
            if (isset($tax[$_shipping])) {
                $tax = $tax[$_shipping];
                $rates = $address->getShippingRatesCollection();
                /* var $rates Mage_Eav_Model_Entity_Collection_Abstract */
                foreach ($rates as $rate) {
                    /* var $rate Mage_Sales_Model_Quote_Address_Rate */
                    if (($name = $rate->getCarrier().'_'.$rate->getMethod()) == $_shipping) {
                        $_tax = $tax['tax'];
                        $_duty = $tax['duty'];
                    }
                }
            }
            $this->_tax = $_tax;
            $this->_duty = $_duty;
            $this->_addAmountToTotal();
            $address->setShippingipTotalTax($_tax);
            $address->setShippingipTotalDuty($_duty);
            if ($_tax+$_duty) {
                Mage::getSingleton('checkout/session')->setTaxDutyTotal(array(
                    'mode' => $this->_getMode(),
                    'tax' => $_tax,
                    'duty' => $_duty
                ));
            }
        }
        return $this;
    }
}
