<?php
$installer = $this;
$installer->startSetup();
if ($installer->getConnection()->isTableExists($installer->getTable('ipglobalecommerce/parcel')) !== true) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('ipglobalecommerce/parcel'))
        ->addColumn('parcel_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  =>  true,
            'unsigned'  =>  true,
            'nullable'  =>  false,
            'primary'   =>  true
        ), 'Id')
        ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  =>  true,
            'nullable'  =>  false,
        ), 'Order Id');
    $installer->getConnection()->createTable($table);
    $installer->getConnection()->addKey($installer->getTable('ipglobalecommerce/parcel'), 'IDX_ORDER', 'order_id', 'UNIQUE');
    $installer->getConnection()->addConstraint('FK_IPARCEL_PARCEL_ORDER_ID', $installer->getTable('ipglobalecommerce/parcel'), 'order_id', $installer->getTable('sales/order'), 'entity_id');
}
$installer->endSetup();