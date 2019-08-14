<?php
$installer = $this;
$connection = $installer->getConnection();

$installer->startSetup();

$config_data_collection = Mage::getResourceModel('core/config_data_collection')
	->addFieldToFilter('path','carriers/i-parcel/whitelabelship');
if($config_data_collection->getSize()){
	$config = array();
	foreach($config_data_collection as $config_data){
		if(!isset($config[$config_data->getScope()])){
			$config[$config_data->getScope()] = array();
		}
		$config[$config_data->getScope()][$config_data->getScopeId()] = $config_data->getValue(); 
	}

	foreach($config as $scope => $scope_data){
		foreach($scope_data as $scopeId => $value){
			$data = array(
				'_'.str_replace('.','_',microtime(true)) => array(
					'service_id' => 115,
					'title'	=> $value
				)
			);

			Mage::getModel('core/config_data')
				->setScope($scope)
				->setScopeId($scopeId)
				->setPath('carriers/i-parcel/name')
				->setValue(serialize($data))
				->save();
		}
	}
}else{
	$data = array(
		'_'.str_replace('.','_',microtime(true)) => array(
			'service_id' => 115,
			'title'	=> 'UPS i-parcel Saver' 
		)
	);
	Mage::getModel('core/config_data')
		->setScope('default')
		->setScopeId(0)
		->setPath('carriers/i-parcel/name')
		->setValue(serialize($data))
		->save();
}

$installer->endSetup();
?>
