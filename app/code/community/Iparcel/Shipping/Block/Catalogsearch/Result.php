<?php
class Iparcel_Shipping_Block_Catalogsearch_Result extends Mage_CatalogSearch_Block_Result
{
    protected function _getProductCollection()
    {
        if (is_null($this->_productCollection)) {
            $this->_productCollection = $this->getListBlock()->getLoadedProductCollection();

            $helper = Mage::helper('shippingip/international');
            // if international customer enabled and it is international customer
            if ($helper->checkEnabled() && $helper->getInternational()) {
                // add international visibility attribute to filter
                $this->_productCollection->addAttributeToFilter($helper->getVisibilityAttributeName(), true);
            }
        }

        return $this->_productCollection;
    }

    public function getLoadedProductCollection()
    {
        return $this->_getProductCollection();
    }

    /**
     * Retrieve qty for product
     *
     * @param Mage_Catalog_Model_Product $product
     * $return float
     */
    public function getStock($product)
    {
        return Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty();
    }
}
