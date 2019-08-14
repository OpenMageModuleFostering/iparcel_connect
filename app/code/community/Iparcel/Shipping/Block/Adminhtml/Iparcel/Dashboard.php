<?php
/**
 * Frontend Model Class for carriers/i-parcel/additionalfields config button
 *
 * @category	 Iparcel 
 * @package    Iparcel_Shipping
 * @author     Patryk Grudniewski <patryk.grudniewski@sabiosystem.com> 
 */
class Iparcel_Shipping_Block_Adminhtml_Iparcel_Dashboard extends Mage_Adminhtml_Block_System_Config_Form_Field{

    /**
     * Get Button Html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element) {
        $url = 'http://globalaccess.i-parcel.com';
        
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel('Go to i-parcel Dashboard')
                    ->setOnClick("window.location.href='" . $url . "'")
                    ->toHtml();

        return $html;
    }
    

    
}
