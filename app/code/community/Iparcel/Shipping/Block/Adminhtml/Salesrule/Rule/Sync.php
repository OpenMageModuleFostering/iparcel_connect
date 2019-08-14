<?php
/**
 * Frontend Model Class for salesrules/upload/sync config button
 *
 * @category     Iparcel
 * @package    Iparcel_Shipping
 * @author     Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Block_Adminhtml_Salesrule_Rule_Sync extends Mage_Adminhtml_Block_System_Config_Form_Field
{

    /**
     * Get Button Html
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $url = Mage::helper('adminhtml')->getUrl("shippingip/sync/salesrule");
        
        $html = $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('scalable')
                    ->setLabel('Synchronize')
                    ->setOnClick("window.location.href='" . $url . "'")
                    ->toHtml();

        return $html;
    }
}
