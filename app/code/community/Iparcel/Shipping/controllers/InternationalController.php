<?php
/**
 * i-parcel International Customer controller
 *
 * @category        Iparcel
 * @package             Iparcel_Shipping
 * @author          Patryk Grudniewski <patryk.grudniewski@sabiosystem.com
 */
class Iparcel_Shipping_InternationalController extends Mage_Core_Controller_Front_Action
{
    /**
     * Preparing headers for external ajax
     */
    protected function _prepareHeaders()
    {
        $this->getResponse()
            ->clearHeaders()
            ->setHeader('Access-Control-Allow-Origin', '*');
    }

    /**
     * Changing international flag to true and printing if there was flag change
     */
    public function enableAction()
    {
        $this->_prepareHeaders();
        $current = Mage::helper('shippingip/international')->getInternational();
        if ($current) {
            echo 'false';
        } else {
            Mage::helper('shippingip/international')->setInternational(true);
            echo 'true';
        }
    }

    /**
     * Changing international flag to false and printing if there was flag change
     */
    public function disableAction()
    {
        $this->_prepareHeaders();
        $current = Mage::helper('shippingip/international')->getInternational();
        if ($current) {
            Mage::helper('shippingip/international')->setInternational(false);
            echo 'true';
        } else {
            echo 'false';
        }
    }
}
