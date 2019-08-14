<?php
/**
 * Iparcel Logs Adminhtml Grid Block
 *
 * @category   Iparcel
 * @package    Iparcel_Shipping
 * @author		 Patryk Grudniewski <patryk.grudniewski@sabiosystem.com> 
 */
class Iparcel_Shipping_Block_Adminhtml_Logs_Grid extends Mage_Adminhtml_Block_Widget_Grid{
	/**
	 * Internal constructor, that is called from real constructor
	 *
	 */
	protected function _construct(){
		$this->setFilterVisibility(false);
		$this->setPagerVisibility(false);
	}

	/**
	 * Prepare grid collection object
	 *
	 * @return Iparcel_Shipping_Block_Adminhtml_Logs_Grid
	 */
	protected function _prepareCollection(){
		$collection = Mage::getModel('shippingip/api_log')->getJson();
		$this->setCollection($collection);

		return parent::_prepareCollection();
	}

	/**
	 * Prepare grid columns
	 *
	 * @return Iparcel_Shipping_Block_Adminhtml_Logs_Grid
	 */
	protected function _prepareColumns(){
		$this->addColumn('timestamp',array(
			'header' => $this->__('Timestamp'),
			'index' => 'timestamp',
			'width' => '200px'
		));
		$this->addColumn('controller',array(
			'header' => $this->__('API Controller'),
			'index' => 'controller',
			'width' => '200px'
		));
		$this->addColumn('request',array(
			'header' => $this->__('Request'),
			'index' => 'request'
		));
		$this->addColumn('response',array(
			'header' => $this->__('Response'),
			'index' => 'response'
		));

		return parent::_prepareColumns();
	}

	/**
	 * Get Grid Row's Class
	 *
	 * $return string
	 */
	public function getRowClass() {
		return 'read';
	}
}
?>
