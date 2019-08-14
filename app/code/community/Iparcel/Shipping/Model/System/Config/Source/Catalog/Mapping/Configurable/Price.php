<?php
/**
 * Source model for catalog_mapping/attributes/price config field
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author      Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_System_Config_Source_Catalog_Mapping_Configurable_Price
{
    const CONFIGURABLE = "0";
    const SIMPLE = "1";

    /**
     * Options list
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => self::CONFIGURABLE, 'label' => Mage::helper('shippingip')->__("Parent product's price")),
            array('value' => self::SIMPLE, 'label' => Mage::helper('shippingip')->__("Default"))
        );
    }
}
