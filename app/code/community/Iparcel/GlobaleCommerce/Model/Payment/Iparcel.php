<?php
/**
 * Class for i-parcel external sales API payment method
 *
 * @category    Iparcel
 * @package     Iparcel_GlobaleCommerce
 * @author      Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_GlobaleCommerce_Model_Payment_Iparcel extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'iparcel';
    protected $_infoBlockType = 'ipglobalecommerce/payment_info';
    protected $_canUseCheckout = false;
    protected $_canUseForMultishipping = false;
    protected $_canUseInternal = false;
}
