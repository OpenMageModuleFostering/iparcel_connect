<?php
/**
 * Model for catalog mapping cron
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_Shipping_Model_Catalog_Mapping
{
    /**
     * Method called by cron
     */
    public function sync()
    {
        $productCollection = Mage::getModel('catalog/product')->getCollection();
        Mage::helper('shippingip/api')->submitCatalog($productCollection);
    }
}
