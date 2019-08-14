<?php
$installer = $this;
$installer->startSetup();
if ($installer->getConnection()->isTableExists($installer->getTable('shippingip/api_order')) === true){
	$installer->getConnection()->dropTable($installer->getTable('shippingip/api_order'));
}
$installer->endSetup();
?>
