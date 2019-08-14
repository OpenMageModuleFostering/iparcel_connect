<?php
$installer = $this;
$installer->startSetup();
$resource = Mage::getSingleton('core/resource');
$config = Mage::getModel('core/config');
$readConnection = $resource->getConnection('core_read');
$query = 'SELECT `path`, `value` FROM '.$resource->getTableName('core/config_data')." WHERE 
	`path` LIKE '%iparcel/catalog_mapping%' OR
	`path` LIKE '%sales/external%' OR
	`path` LIKE '%iparcel/config%' OR
	`path` LIKE '%carriers/i-parcel/custid' OR
	`path` LIKE '%carriers/i-parcel/userid'";
foreach ($readConnection->fetchAll($query) as $variable){
	$uri = explode('/',$variable['path']);
	switch($variable['path']){
		case 'carriers/i-parcel/custid':
		case 'carriers/i-parcel/userid':
			$config->saveConfig('iparcel/config/'.$uri[2],$variable['value']);
			break;
		case 'iparcel/catalog_mapping/attribute1':
		case 'iparcel/catalog_mapping/attribute2':
		case 'iparcel/catalog_mapping/attribute3':
		case 'iparcel/catalog_mapping/attribute4':
		case 'iparcel/catalog_mapping/attribute5':
		case 'iparcel/catalog_mapping/attribute6':
		case 'iparcel/catalog_mapping/hscodeus':
		case 'iparcel/catalog_mapping/shipalone':
			$config->saveConfig('catalog_mapping/attributes/'.$uri[2],$variable['value']);
			break;
		case 'sales/external/enabled':
		case 'sales/external/transactional_emails':
			$config->saveConfig('external_api/sales'.$uri[2],$variable['value']);
			break;
		case 'iparcel/config/scripts':
		case 'iparcel/config/jquery':
			$config->saveConfig('iparcel/scripts'.$uri[2],$variable['value']);
			break;
		case 'iparcel/catalog_mapping/upload_from':
		case 'iparcel/catalog_mapping/upload_to':
			$config->saveConfig('catalog_mapping/upload/'.$uri[2]=='upload_to' ? 'to' : 'from',$variable['value']);
			break;
	}
}
$installer->endSetup();
?>
