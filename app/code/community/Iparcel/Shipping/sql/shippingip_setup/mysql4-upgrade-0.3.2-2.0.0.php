<?php
$installer = $this;
$installer->startSetup();
if ($installer->getConnection()->isTableExists($installer->getTable('shippingip/cpf_order')) !== true) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('shippingip/cpf_order'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  =>  true,
            'unsigned'  =>  true,
            'nullable'  =>  false,
            'primary'   =>  true
        ), 'Id')
        ->addColumn('order_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  =>  true,
            'nullable'  =>  false,
            'unique'    =>  true
        ), 'Order Id')
        ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'default'   =>  '',
            'nullable'  =>  false,
        ), 'Value');
    $installer->getConnection()->createTable($table);
    $installer->getConnection()->addKey($installer->getTable('shippingip/cpf_order'), 'IDX_ORDER', 'order_id');
    $installer->getConnection()->addConstraint('FK_IPARCEL_CPF_ORDER_ID', $installer->getTable('shippingip/cpf_order'), 'order_id', $installer->getTable('sales/order'), 'entity_id');
}
if ($installer->getConnection()->isTableExists($installer->getTable('shippingip/cpf_quote')) !== true) {
    $table = $installer->getConnection()
        ->newTable($installer->getTable('shippingip/cpf_quote'))
        ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity'  =>  true,
            'unsigned'  =>  true,
            'nullable'  =>  false,
            'primary'   =>  true
        ), 'Id')
        ->addColumn('quote_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned'  =>  true,
            'nullable'  =>  false,
            'unique'    =>  true
        ), 'Quote Id')
        ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
            'default'   =>  '',
            'nullable'  =>  false,
        ), 'Value');
    $installer->getConnection()->createTable($table);
    $installer->getConnection()->addKey($installer->getTable('shippingip/cpf_quote'), 'IDX_QUOTE', 'quote_id');
    $installer->getConnection()->addConstraint('FK_IPARCEL_CPF_QUOTE_ID', $installer->getTable('shippingip/cpf_quote'), 'quote_id', $installer->getTable('sales/quote'), 'entity_id');
}
$installer->endSetup();
