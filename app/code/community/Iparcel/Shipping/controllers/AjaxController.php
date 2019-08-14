<?php
/**
 * i-parcel frontend ajax controller
 *
 * @category	Iparcel
 * @package		Iparcel_Shipping
 * @author		Patryk Grudniewski <patryk.grudniewski@sabiosystem.com>
 */
class Iparcel_Shipping_AjaxController extends Mage_Core_Controller_Front_Action{
	/**
	 * Configurable products action for post script
	 */
	public function configurableAction(){
		$sku = $this->getRequest()->getParam('sku');
		$super_attribute = $this->getRequest()->getParam('super_attribute');
		// var $product Mage_Catalog_Model_Product
		$product = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
		// var $child Mage_Catalog_Model_Product
		$child = Mage::getModel('catalog/product_type_configurable')->getProductByAttributes($super_attribute,$product);
		// var $typeInstance Mage_Catalog_Model_Product_Type_Abstract
		$typeInstance = $product->getTypeInstance(true);
		if (!$typeInstance instanceof Mage_Catalog_Model_Product_Type_Configurable){
			return;
		}
		$attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
		// var $attributes array
		$options = array();
		foreach ($attributes as $attribute){
			$id = $attribute->getAttributeId();
			foreach ($attribute->getPrices() as $value){
				if ($value['value_index'] == $super_attribute[$id]){
					$options[$attribute->getProductAttribute()->getAttributeCode()] = $value['label'];
					break;
				}
			}
		}
		if ($child){
			$this->getResponse()
				->clearHeaders()
				->setHeader('Content-Type', 'application/json');
			echo json_encode(array(
				'sku' => $child->getSku(),
				'attributes' => $options,
				'stock' => Mage::getModel('cataloginventory/stock_item')->loadByProduct($child)->getQty()
			));
		}
	}
}
?>
