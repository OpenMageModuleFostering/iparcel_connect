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

if ($installer->getConnection()->isTableExists($installer->getTable('shippingip/cpf')) !== true){
	$table = $installer->getConnection()
		->newTable($installer->getTable('shippingip/cpf'))
		->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'identity'	=> 	true,
			'unsigned'	=>	true,
			'nullable'	=>	false,
			'primary'	=>	true
		), 'Id')
		->addColumn('country_code', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
			'nullable'	=>	false,
			'unique'	=>	true
		), 'Country Code')
		->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
			'nullable'	=>	false
		), 'Name')
		->addColumn('required', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
			'nullable'	=>	false,
			'default'	=>	false
		), 'Required');
	$installer->getConnection()->createTable($table);

	$cpf = Mage::getModel('shippingip/cpf');
	$cpf->setCountryCode('BR');
	$cpf->setName('CPF');
	$cpf->save();
	$cpf = Mage::getModel('shippingip/cpf');
	$cpf->setCountryCode('KR');
	$cpf->setName('Control Number');
	$cpf->save();
}

if ($installer->getConnection()->isTableExists($installer->getTable('shippingip/cpf_order')) !== true){
	$table = $installer->getConnection()
		->newTable($installer->getTable('shippingip/cpf_order'))
		->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'identity'	=> 	true,
			'unsigned'	=>	true,
			'nullable'	=>	false,
			'primary'	=>	true
		), 'Id')
		->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'unsigned'	=>	true,
			'nullable'	=> 	false,
			'unique'	=>	true
		), 'Order Id')
		->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
			'default'	=>	'',
			'nullable'	=>	false,
		), 'Value');
	$installer->getConnection()->createTable($table);
	$installer->getConnection()->addKey($installer->getTable('shippingip/cpf_order'), 'IDX_ORDER', 'order_id');
	$installer->getConnection()->addConstraint('FK_IPARCEL_CPF_ORDER_ID',$installer->getTable('shippingip/cpf_order'),'order_id',$installer->getTable('sales/order'),'entity_id');
}
if ($installer->getConnection()->isTableExists($installer->getTable('shippingip/cpf_quote')) !== true){
	$table = $installer->getConnection()
		->newTable($installer->getTable('shippingip/cpf_quote'))
		->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'identity'	=> 	true,
			'unsigned'	=>	true,
			'nullable'	=>	false,
			'primary'	=>	true
		), 'Id')
		->addColumn('quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'unsigned'	=>	true,
			'nullable'	=> 	false,
			'unique'	=>	true
		), 'Quote Id')
		->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
			'default'	=>	'',
			'nullable'	=>	false,
		), 'Value');
	$installer->getConnection()->createTable($table);
	$installer->getConnection()->addKey($installer->getTable('shippingip/cpf_quote'), 'IDX_QUOTE', 'quote_id');
	$installer->getConnection()->addConstraint('FK_IPARCEL_CPF_QUOTE_ID',$installer->getTable('shippingip/cpf_quote'),'quote_id',$installer->getTable('sales/quote'),'entity_id');
}

$installer->endSetup();
?>
