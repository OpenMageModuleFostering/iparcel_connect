<?php
/**
 * @category    Iparcel
 * @package     Iparcel_Shipping
 * @author     Bobby Burden <bburden@i-parcel.com>
 */
class Iparcel_Shipping_Model_Observer
{
    const MAGE_ORDER_MODEL = 'Mage_Sales_Model_Order';
    const IPARCEL_ORDER_MODEL = 'Iparcel_Shipping_Model_Sales_Order';
    const IPARCEL_DYNAMIC_ORDER_MODEL = 'Iparcel_Shipping_Model_Sales_Order_Extend';

    protected $_eventFlag = true;
    protected $_filePath = null;

    public function __construct()
    {
        $this->_filePath = Mage::getBaseDir('cache') . DS . self::IPARCEL_DYNAMIC_ORDER_MODEL . '.php';
    }

    /**
     * Create a class dynamically and apply _checkState() function by i-parcel extension
     */
    public function createCheckState($observer)
    {
        if ($this->_isOrderCreatedByExtension() && $this->_eventFlag) {
            $orderClass = self::MAGE_ORDER_MODEL;
            $iparcelOrderClass = self::IPARCEL_ORDER_MODEL;
            //Get class which overrides Mage_Sales_Model_Order
            $orderRewriteClass = Mage::getConfig()->getNode('global/models/sales/rewrite/order');

            //If there is any class, use it. Otherwise, use Magento default class
            if ($orderRewriteClass) {
                $orderClass = $orderRewriteClass[0];
            }

            //Create a class dynamically and apply _checkState() function by i-parcel extension
            $dynamicOrderString = "<?php
                    class $iparcelOrderClass extends $orderClass
                    {
                        protected function _checkState()
                        {
                            if (Mage::registry('isExternalSale') && Mage::getStoreConfig('external_api/sales/order_status') != Mage_Sales_Model_Order::STATE_COMPLETE) {
                                return \$this;
                            } else {
                                return parent::_checkState();
                            }
                        }
                    }
                ";

            $cacheStatus = Mage::app()->getCacheInstance()->canUse('config');

            //if cache is disabled or file does not exist, write $dynamicOrderString to file
            if (!$cacheStatus || !file_exists($this->_filePath)) {
                file_put_contents($this->_filePath, $dynamicOrderString);
            }
            include $this->_filePath;

            //to avoid file content is changed by anyone
            $fileContent = file_get_contents($this->_filePath);
            if ($fileContent != $dynamicOrderString) {
                file_put_contents($this->_filePath, $dynamicOrderString);
                include $this->_filePath;
            }

            Mage::getConfig()->setNode('global/models/sales/rewrite/order', $iparcelOrderClass);
            $this->_eventFlag = false;
        }
    }

    /**
     * Check to ensure that event model_load_before occurs when order is created by i-parcel extension
     *
     * @return boolean
     */
    protected function _isOrderCreatedByExtension()
    {
        return Mage::registry('isExternalSale') == true;
    }

    /**
     * Remove Iparcel_Shipping_Model_Sales_Order_Extend file when flush or refresh cache
     */
    public function removeExtendFile($observer)
    {
        if (file_exists($this->_filePath)) {
            unlink($this->_filePath);
        }
    }
}
