<?php
/**
 * Frontend Model Class for catalog_mapping/upload/upload config button
 *
 * @category	 Iparcel 
 * @package    Iparcel_Shipping
 * @author     Patryk Grudniewski <patryk.grudniewski@sabiosystem.com> 
 */
class Iparcel_Shipping_Block_Adminhtml_Catalog_Mapping_Button extends Mage_Adminhtml_Block_System_Config_Form_Field{

    /**
     * Get Button Html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $url = Mage::helper('adminhtml')->getUrl("shippingip/sync_ajax/catalog");
        
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel('Upload Now')
                    ->setOnClick("window.location.href='" . $url . "'")
                    ->toHtml();

        return $html;
    }
    

    
}
