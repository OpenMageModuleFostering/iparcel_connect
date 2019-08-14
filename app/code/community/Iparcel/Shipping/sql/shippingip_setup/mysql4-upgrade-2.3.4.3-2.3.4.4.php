<?php
$installer = $this;
$installer->startSetup();
if (file_exists(Mage::getBaseDir('log').'/'.Iparcel_Shipping_Model_Api_Log::LOG_FILENAME)) {
    unlink(Mage::getBaseDir('log').'/'.Iparcel_Shipping_Model_Api_Log::LOG_FILENAME);
}
$installer->endSetup();
