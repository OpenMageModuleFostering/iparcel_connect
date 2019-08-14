<?php
$installer = $this;
$connection = $installer->getConnection();
$data = Mage::getModel('ipglobalecommerce/parcel')->getCollection()->toArray();
$data = $data['items'];

$installer->startSetup();
if ($connection->isTableExists($installer->getTable('ipglobalecommerce/parcel'))) {
    $connection->dropTable($installer->getTable('ipglobalecommerce/parcel'));
}
$table = $installer->getConnection()
    ->newTable($installer->getTable('ipglobalecommerce/parcel'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  =>  true,
        'unsigned'  =>  true,
        'nullable'  =>  false,
        'primary'   =>  true
    ), 'Id')
    ->addColumn('parcel_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  =>  true,
        'nullable'  =>  false,
    ), 'Order Id')
    ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  =>  true,
        'nullable'  =>  false,
    ), 'Order Id');
$installer->getConnection()->createTable($table);
$installer->getConnection()->addKey($installer->getTable('ipglobalecommerce/parcel'), 'IDX_ORDER', 'order_id', 'UNIQUE');
$installer->getConnection()->addConstraint('FK_IPARCEL_PARCEL_ORDER_ID', $installer->getTable('ipglobalecommerce/parcel'), 'order_id', $installer->getTable('sales/order'), 'entity_id');
$installer->endSetup();