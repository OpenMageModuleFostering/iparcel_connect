<?php
/**
 * Source model class for external_api/sales/order_status config field
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author      Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_System_Config_Source_Sales_Order_Status
{
    /**
     * Options list
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => Mage_Sales_Model_Order::STATE_COMPLETE, 'label' => Mage::helper('shippingip')->__('Complete')),
            array('value' => 'pending', 'label' => Mage::helper('shippingip')->__('Pending')),
            array('value' => Mage_Sales_Model_Order::STATE_PROCESSING, 'label' => Mage::helper('shippingip')->__('Processing'))
        );
    }
}
