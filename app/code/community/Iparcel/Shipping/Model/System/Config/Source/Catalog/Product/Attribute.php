<?php
/**
 * Source model class for backend config fields with product atributes list
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author      Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_System_Config_Source_Catalog_Product_Attribute
{
    /**
     * Options list
     *
     * @return array
     */
    public function toOptionArray()
    {
        $_attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')->addVisibleFilter();
        /* var $_attributeCollection Mage_Catalog_Model_Resource_Product_Attribute_Collecton */
        $attributeCollection = array(array('value' => 0, 'label' => '<empty>'));
        foreach ($_attributeCollection as $_attribute) {
            /* var $_attribute Mage_Catalog_Model_Product_Attribute_Collection */
            $label = $_attribute->getFrontendLabel();
            $attributeCollection[] = array(
                'value' => $_attribute->getAttributeId(),
                'label' => (empty($label)) ? Mage::helper('catalog')->__($_attribute->getAttributeCode()) : Mage::helper('catalog')->__($label)
            );
        }
        return $attributeCollection;
    }
}
