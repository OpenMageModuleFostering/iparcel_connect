<?php
/**
 * Adminhtml i-parcel logs controller
 *
 * @category    Iparcel
 * @package         Iparcel_Shipping
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_Shipping_Adminhtml_Shippingip_LogController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Init adminhtml response
     *
     * @return Iparcel_Shipping_Adminhtml_LogController
     */
    protected function _init()
    {
        $this->loadLayout()
            ->_setActiveMenu('iparcel/log')
            ->_title($this->__('Logs'))->_title($this->__('i-parcel Shipping'))
            ->_addBreadcrumb($this->__('Logs'), $this->__('Logs'))
            ->_addBreadcrumb($this->__('i-parcel Shipping'), $this->__('iparcel Shipping'));
        return $this;
    }

    /**
     * Show grid action
     */
    public function indexAction()
    {
        $this->_init()
            ->renderLayout();
    }

    /**
     * Clear log action
     */
    public function clearAction()
    {
        if (Mage::getModel('shippingip/api_log')->clear() === false) {
            Mage::getSingleton('core/session')->addError($this->__('Unable to write to the log file.'));
        }
        $this->_redirect('*/*/index');
    }
}
