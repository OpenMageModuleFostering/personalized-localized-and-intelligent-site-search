<?php

class Tagalys_SimilarProducts_Block_Catalog_Product_SimilarProducts extends Mage_Catalog_Block_Product_List_Related
{
    protected $_itemCollection;
    protected function _prepareData()
    {
    	// die('inside block');
    	$service = Mage::getModel("Tagalys_SimilarProducts_Model_Client");
    	$tagalys = Mage::helper("similarproducts")->isTagalysActive();
	//die(var_dump($tagalys));
    	if ($tagalys) {
    		$product = Mage::registry('product');
    		// $product = Mage::registry('current_product');
    		/* @var $product Mage_Catalog_Model_Product */
    		if(isset($product)) {
    			$product_id = $product->getId();
    		} else {
    			return;
    		}
    		$result = $service->similarProducts($product_id, 15);
    		// die(var_dump($result));
    		$product_ids = ($result['results']) ? $result['results'] : null;
    		// $result =  Mage::helper("tglssearch")->getTagalysSearchData();
    		$this->_itemCollection = Mage::getResourceModel('catalog/product_collection')
    		->setStore($this->_storeId)
    		->addAttributeToSelect('*')
    		->addAttributeToFilter( 'entity_id', array( 'in' => $product_ids ));
		//die(var_dump($product_ids));
    		$orderString = array('CASE e.entity_id');
    			
			foreach($product_ids as $i => $productId) {
				$orderString[] = 'WHEN '.$productId.' THEN '.$i;
			}
			$orderString[] = 'END';
			$orderString = implode(' ', $orderString);
			$this->_itemCollection->getSelect()->order(new Zend_Db_Expr($orderString));
			$this->title = "Similar Products";
    		// $this->_itemCollection->load();
    		return $this;
    	} else {
    		parent::_prepareData();
    	}
    }
}
