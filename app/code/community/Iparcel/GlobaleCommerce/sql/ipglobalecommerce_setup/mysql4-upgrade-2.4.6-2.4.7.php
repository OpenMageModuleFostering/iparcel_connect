<?php
$installer = $this;
$connection = $installer->getConnection();

$installer->startSetup();

/**
 * This installer adds a new database table to keep track of inserted orders.
 * This allows for the same Order/Add request to be sent without creating
 * duplicate Magento orders.
 */
$installer->run("
DROP TABLE IF EXISTS {$this->getTable('ipglobalecommerce/api_order')};
CREATE TABLE {$this->getTable('ipglobalecommerce/api_order')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `tracking_number` varchar(255) NOT NULL,
  `order_increment_id` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();