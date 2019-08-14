<?php
/**
 * Source model class for backend config fields with boolean product atributes
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author      Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Model_System_Config_Source_Catalog_Product_Attribute_Boolean
{
    /**
     * Options list
     *
     * @return array
     */
    public function toOptionArray()
    {
        $_attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addVisibleFilter()
            ->addFieldToFilter('frontend_input', 'boolean');
        /* var $_attributeCollection Mage_Catalog_Model_Resource_Product_Attribute_Collection */
        $attributeCollection = array(array('value' => 0, 'label' => '<empty>'));
        foreach ($_attributeCollection as $_attribute) {
            /* var $_attribute Mage_Catalog_Model_Product_Attribute */
            $label = $_attribute->getFrontendLabel();
            $attributeCollection[] = array(
                'value' => $_attribute->getAttributeId(),
                'label' => (empty($label)) ? Mage::helper('catalog')->__($_attribute->getAttributeCode()) : Mage::helper('catalog')->__($label)
            );
        }
        return $attributeCollection;
    }
}
