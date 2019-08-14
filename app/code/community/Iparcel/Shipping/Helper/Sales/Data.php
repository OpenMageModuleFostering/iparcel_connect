<?php
/**
 * Sales module base helper extended with i-parcel orders surpressing emails
 *
 * @category    Iparcel
 * @package     Iparcel_Shipping
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_Shipping_Helper_Sales_Data extends Mage_Sales_Helper_Data
{
    /**
     * Check allow to send new order confirmation email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendNewOrderConfirmationEmail($store = null)
    {
        $parent = parent::canSendNewOrderConfirmationEmail($store);
        return Mage::registry('isExternalSale') ? $parent && !Mage::getStoreConfigFlag('external_api/sales/transactional_emails') : $parent;
    }

    /**
     * Check allow to send new order email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendNewOrderEmail($store = null)
    {
        $parent = parent::canSendNewOrderEmail($store);
        return Mage::registry('isExternalSale') ? $parent && !Mage::getStoreConfigFlag('external_api/sales/transactional_emails') : $parent;
    }

    /**
     * Check allow to send order comment email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendOrderCommentEmail($store = null)
    {
        $parent = parent::canSendOrderCommentEmail($store);
        return Mage::registry('isExternalSale') ? $parent && !Mage::getStoreConfigFlag('external_api/sales/transactional_emails') : $parent;
    }

    /**
     * Check allow to send new shipment email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendNewShipmentEmail($store = null)
    {
        $parent = parent::canSendNewShipmentEmail($store);
        return Mage::registry('isExternalSale') ? $parent && !Mage::getStoreConfigFlag('external_api/sales/transactional_emails') : $parent;
    }

    /**
     * Check allow to send shipment comment email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendShipmentCommentEmail($store = null)
    {
        $parent = parent::canSendShipmentCommentEmail($store);
        return Mage::registry('isExternalSale') ? $parent && !Mage::getStoreConfigFlag('external_api/sales/transactional_emails') : $parent;
    }

    /**
     * Check allow to send new invoice email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendNewInvoiceEmail($store = null)
    {
        $parent = parent::canSendNewInvoiceEmail($store);
        return Mage::registry('isExternalSale') ? $parent && !Mage::getStoreConfigFlag('external_api/sales/transactional_emails') : $parent;
    }

    /**
     * Check allow to send invoice comment email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendInvoiceCommentEmail($store = null)
    {
        $parent = parent::canSendInvoiceCommentEmail($store);
        return Mage::registry('isExternalSale') ? $parent && !Mage::getStoreConfigFlag('external_api/sales/transactional_emails') : $parent;
    }

    /**
     * Check allow to send new creditmemo email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendNewCreditmemoEmail($store = null)
    {
        $parent = parent::canSendNewCreditmemoEmail($store);
        return Mage::registry('isExternalSale') ? $parent && !Mage::getStoreConfigFlag('external_api/sales/transactional_emails') : $parent;
    }

    /**
     * Check allow to send creditmemo comment email
     *
     * @param mixed $store
     * @return bool
     */
    public function canSendCreditmemoCommentEmail($store = null)
    {
        $parent = parent::canSendCreditmemoCommentEmail($store);
        return Mage::registry('isExternalSale') ? $parent && !Mage::getStoreConfigFlag('external_api/sales/transactional_emails') : $parent;
    }
}
