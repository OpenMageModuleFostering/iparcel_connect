<?php
/**
 * i-parcel cpf block
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_Shipping_Block_Cpf extends Mage_Core_Block_Template
{
    /**
     * Geting formated array with cpfs list
     *
     * @return array
     */
    public function getCpf()
    {
        $cpfCollection = Mage::getModel('shippingip/cpf')->getCollection();
        $response = array();
        foreach ($cpfCollection as $cpf) {
            $response[$cpf->getCountryCode()] = array(
                'name'          =>  $cpf->getName(),
                'required'      =>  $cpf->getRequired()
            );
        }
        return $response;
    }
}
