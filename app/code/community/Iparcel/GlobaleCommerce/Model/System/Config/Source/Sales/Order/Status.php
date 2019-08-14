<?php
/**
 * Source model class for external_api/sales/order_status config field
 *
 * @category    Iparcel
 * @package     Iparcel_GlobaleCommerce
 * @author      Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_GlobaleCommerce_Model_System_Config_Source_Sales_Order_Status
{
    /**
     * Options list
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => Mage_Sales_Model_Order::STATE_COMPLETE, 'label' => Mage::helper('ipglobalecommerce')->__('Complete')),
            array('value' => 'pending', 'label' => Mage::helper('ipglobalecommerce')->__('Pending')),
            array('value' => Mage_Sales_Model_Order::STATE_PROCESSING, 'label' => Mage::helper('ipglobalecommerce')->__('Processing'))
        );
    }
}
