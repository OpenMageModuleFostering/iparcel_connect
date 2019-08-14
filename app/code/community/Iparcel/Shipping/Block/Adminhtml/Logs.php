<?php
/**
 * Adminhtml i-parcel logs grid container block
 *
 * @category   Iparcel
 * @package    Iparcel_Shipping
 * @author     Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_Block_Adminhtml_Logs extends Mage_Adminhtml_Block_Widget_Grid_Container{
	/**
	 * Initialize factory instance
	 */
	public function __construct(){
		$this->_blockGroup = 'shippingip';
		$this->_controller = 'adminhtml_logs';
		$this->_headerText = $this->__('Logs');

		parent::__construct();
	}

	/**
	 * Preparing child blocks for each added button ,removing add button, adding clear button
	 *
	 * @return Iparcel_Shipping_Block_Adminhtml_Logs 
	 */
	protected function _prepareLayout()
	{
		$this->_removeButton('add');

		$this->_addButton('clear',array(
			'label' => $this->__('Clear'),
			'onclick' => 'setLocation(\''.$this->getUrl('*/*/clear').'\')'
		));

		return parent::_prepareLayout();
	}
}
?>
