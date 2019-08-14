<?php
/**
 * Class for i-parcel external sales API payment method
 *
 * @category	Iparcel
 * @package		Iparcel_Shipping
 * @author		Patryk Grudniewski
 */
class Iparcel_Shipping_Model_Payment_Iparcel extends Mage_Payment_Model_Method_Abstract{
	protected $_code = 'iparcel';
	protected $_infoBlockType = 'shippingip/payment_info';
	protected $_canUseCheckout = false;
	protected $_canUseForMultishipping = false;
	protected $_canUseInternal = false;
}
?>
