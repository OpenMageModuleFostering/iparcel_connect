<?php
/**
 * Adminhtml i-parcel sync controller
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author      Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Adminhtml_SyncController extends Mage_Adminhtml_Controller_Action
{
    public function salesruleAction()
    {
        $_salesRuleCollection = Mage::getResourceModel('salesrule/rule_collection');
        /* var $_salesRuleCollection Mage_SalesRule_Model_Resource_Rule_Collection */
        Mage::helper('shippingip/api')->salesRule($_salesRuleCollection);
        $this->_redirectReferer();
    }
}
