<?php
$installer = $this;
$installer->startSetup();
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

$installer->endSetup();
?>
