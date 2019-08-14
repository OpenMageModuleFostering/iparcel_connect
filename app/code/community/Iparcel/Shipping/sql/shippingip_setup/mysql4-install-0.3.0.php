<?php
$installer = $this;
$installer->startSetup();
if ($installer->getConnection()->isTableExists($installer->getTable('shippingip/api_order')) !== true){
	$table = $installer->getConnection()
		->newTable($installer->getTable('shippingip/api_order'))
		->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'identity'	=>	true,
			'unsigned'	=>	true,
			'nullable'	=>	false,
			'primary'	=>	true
		), 'Id')
		->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'unsigned'	=>	true,
			'nullable'	=> 	false
		), 'Order Id')
		->addColumn('transactional_emails', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
			'nullable'	=>	false,
			'default'	=>	1
		), 'Transactional Emails Enabled');
	$installer->getConnection()->createTable($table);
	$installer->getConnection()->addKey($installer->getTable('shippingip/api_order'), 'IDX_ORDER', 'order_id');
	$installer->getConnection()->addConstraint('FK_IPARCEL_API_ORDER_ID',$installer->getTable('shippingip/api_order'),'order_id',$installer->getTable('sales/order'),'entity_id');
}
$installer->endSetup();
?>
