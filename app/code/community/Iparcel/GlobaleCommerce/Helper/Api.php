<?php
/**
 * I-parcel sending API helper
 *
 * This helper facilitates the connection to the API web service documented
 * here: http://webservices.i-parcel.com/Help
 *
 * @category    Iparcel
 * @package     Iparcel_GlobaleCommerce
 * @author      Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_GlobaleCommerce_Helper_Api extends Iparcel_All_Helper_Api
{
    /**
     * Send Sales Rule request
     *
     * Takes the passed SalesRules and transforms it into data that can be
     * passed to the API. It then submits the request as JSON to the
     * SalesRule API endpoint.
     *
     * @param Mage_SalesRule_Model_Resource_Rule_Collection Collection of SalesRules to submit
     * @return string Response from the SalesRule request
     */
    public function salesRule(Mage_SalesRule_Model_Resource_Rule_Collection $_collection)
    {
        $log = Mage::getModel('iparcel/log');
        $log->setController('Sales Rule');

        $json = array();
        $json['key'] = Mage::helper('ipglobalecommerce')->getGuid();
        $json['ItemDetailsList'] = array();

        foreach ($_collection as $_rule) {
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

        // FIXME: This API Call is no longer used. Should it be removed?
        $response = $this->_restJSON($json, null);

        $log->setResponse($response);
        $log->save();

        return json_decode($response);
    }
}
