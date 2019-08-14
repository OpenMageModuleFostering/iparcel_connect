<?php
/**
 * i-parcel's rewritement of tax/calculation class
 *
 * @category	Iparcel
 * @package		Iparcel_Shipping
 * @author		Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_Tax_Calculation extends Mage_Tax_Model_Calculation{
	/**
	 * Get calculation tax rate by specific request
	 *
	 * @param   Varien_Object $request
	 * @return  float
	 */
	public function getRate($request)
	{
		if (!$request->getCountryId() || !$request->getCustomerClassId() || !$request->getProductClassId()) {
			return 0;
		}
		/* var $quote Mage_Sales_Model_Quote */
		$quote = Mage::getSingleton('checkout/session')->getQuote();
		// if no quote go to parent
		// if i-parcel tax&duty intercepting is disabled go to parent
		// if quote is virtual go to parent
		// if not i-parcel shipping go to parent
		// if not domestic shipping go to parent
		if (!$quote->getId()
			|| Mage::getStoreConfig('iparcel/tax/mode') == Iparcel_Shipping_Model_System_Config_Source_Tax_Mode::DISABLED
			|| $quote->isVirtual()
			|| explode('_',$quote->getShippingAddress()->getShippingMethod())[0] != 'i-parcel'
			|| Mage::getStoreConfig('general/store_information/merchant_country') == $request->getCountryId()){
			return parent::getRate($request);
		}

		/* var $totals array */
		$totals = $quote->getTotals();
		if (isset($totals['tax'])){
			$totals['tax']->setValue(0);
		}

		// set tax rate value to 0
		$cacheKey = $this->_getRequestCacheKey($request);
		/* var $cacheKey string */
		if (!isset($this->_rateCache[$cacheKey])) {
			$this->unsCalculationProcess();
			$this->unsEventModuleId();
			$this->setRateValue(0);
			Mage::dispatchEvent('tax_rate_data_fetch', array('request'=>$request));
			$this->setCalculationProcess($this->_formCalculationProcess());
			$this->_rateCache[$cacheKey] = $this->getRateValue();
			$this->_rateCalculationProcess[$cacheKey] = $this->getCalculationProcess();
		}
		return $this->_rateCache[$cacheKey];
	}
}
?>
