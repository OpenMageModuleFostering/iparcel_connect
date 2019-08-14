<?php
$installer = $this;
$installer->startSetup();
if ($installer->getConnection()->isTableExists($installer->getTable('ipglobalecommerce/api_order')) === true) {
    $installer->getConnection()->dropTable($installer->getTable('ipglobalecommerce/api_order'));
}
$installer->endSetup();