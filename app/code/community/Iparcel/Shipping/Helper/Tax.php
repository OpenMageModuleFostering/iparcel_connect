<?php
/**
 * i-parcel helper for shipping tax&duty
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_Shipping_Helper_Tax extends Mage_Core_Helper_Abstract
{
    /**
     * Getting shipping tax&duty totals for order
     *
     * @param Mage_Sales_Model_Order $order
     * @return Varien_Data_Connection
     */
    public function getTotal(Mage_Sales_Model_Order $order)
    {
        $taxduty = Mage::getModel('shippingip/tax_totals')->loadByOrderId($order->getId());
        /* var $taxduty Iparcel_Shipping_Model_Tax_Totals */
        $collection = new Varien_Data_Collection();
        switch ($taxduty->getMode()) {
            case '2':
                $collection->addItem(new Varien_Object(array(
                    'code' => 'shippingip_duty',
                    'value' => $taxduty->getDuty(),
                    'base_value' => $taxduty->getDuty(),
                    'label' => Mage::getStoreConfig('iparcel/tax/duty_label')
                )));
                $collection->addItem(new Varien_Object(array(
                    'code' => 'shippingip_tax',
                    'value' => $taxduty->getTax(),
                    'base_value' => $taxduty->getTax(),
                    'label' => Mage::getStoreConfig('iparcel/tax/tax_label')
                )));
                break;
            case '1':
                $collection->addItem(new Varien_Object(array(
                    'code' => 'shippingip_tax',
                    'value' => $taxduty->getTax()+$taxduty->getDuty(),
                    'base_value' => $taxduty->getTax()+$taxduty->getDuty(),
                    'label' => Mage::getStoreConfig('iparcel/tax/tax_duty_label')
                )));
                break;
        }
        return $collection;
    }
}
