<?php
$installer = $this;
$installer->startSetup();
if ($installer->getConnection()->isTableExists($installer->getTable('shippingip/tax_totals')) !== true){
	$table = $installer->getConnection()
		->newTable($installer->getTable('shippingip/tax_totals'))
		->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'identity'	=> 	true,
			'unsigned'	=>	true,
			'nullable'	=>	false,
			'primary'	=>	true
		), 'Id')
		->addColumn('tax', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
			'nullable'	=>	false
		), 'Tax')
		->addColumn('duty', Varien_Db_Ddl_Table::TYPE_FLOAT, null, array(
			'nullable'	=>	true
		), 'Duty')
		->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'unsigned'	=>	true,
			'nullable'	=> 	false
		), 'Order Id')
		->addColumn('mode', Varien_Db_Ddl_Table::TYPE_TINYINT, null, array(
			'nullable'	=>	false,
			'default'	=>	0
		), 'Mode');
	$installer->getConnection()->createTable($table);
	$installer->getConnection()->addKey($installer->getTable('shippingip/tax_totals'), 'IDX_ORDER', 'order_id');
	$installer->getConnection()->addConstraint('FK_IPARCEL_TOTALS_ORDER_ID',$installer->getTable('shippingip/tax_totals'),'order_id',$installer->getTable('sales/order'),'entity_id');
}
$installer->endSetup();
?>
