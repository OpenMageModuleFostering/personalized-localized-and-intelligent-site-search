<?php

class Tagalys_Tsearch_Block_Catalogsearch_Layer_View extends Mage_Catalog_Block_Layer_View {

    protected $_filterBlocks = null;
    protected $_helper = null;
    //to-do
    public function __construct() {
        parent::__construct();
        // $this->_helper = Mage::helper('layerednav');

        if (Mage::helper('tsearch')->isTagalysActive()) {
    
      $this->_categoryBlockName = 'tsearch/catalog_layer_filter_category';
      $this->_attributeFilterBlockName = 'tsearch/catalog_layer_filter_attribute';
      $this->_priceFilterBlockName = 'tsearch/catalog_layer_filter_price';
      // $this->_decimalFilterBlockName = 'tsearch/catalog_layer_filter_decimal';
      // $this->_booleanFilterBlockName   = 'tsearch/catalog_layer_filter_boolean';
    } else {
         $this->_categoryBlockName = 'catalog/layer_filter_category';
      $this->_attributeFilterBlockName = 'catalog/layer_filter_attribute';
      $this->_priceFilterBlockName = 'catalog/layer_filter_price';
    }
    }
    
    // public function getStateInfo() { 
    //     $_hlp = $this->_helper;
    //     //Check the Layered Nav position (Search or Catalog pages)
    //     $ajaxUrl = '';
    //     if ($_hlp->isSearch()) {
    //         $ajaxUrl = Mage::getUrl('catalogsearch/result/index');
    //     } 


    //     $ajaxUrl = $_hlp->stripQuery($ajaxUrl);
    //     $url = $_hlp->getContinueShoppingUrl();

    //     //Set the AJAX Pagination
    //     $pageKey = Mage::getBlockSingleton('page/html_pager')->getPageVarName();

    //     //Get parameters of serach
    //     $queryStr = $_hlp->getParams(true, $pageKey);
    //     if ($queryStr)
    //         $queryStr = substr($queryStr, 1);

    //     $this->setClearAllUrl($_hlp->getClearAllUrl($url));

    //     if (false !== strpos($url, '?')) {
    //         $url = substr($url, 0, strpos($url, '?'));
    //     }
    //     return array($url, $queryStr, $ajaxUrl);
    // }

    // public function bNeedClearAll() {
    //     return $this->_helper->bNeedClearAll();
    // }
    public function getClearUrl()
    {   

        $params['_current']     = true;
        $params['_use_rewrite'] = true;
        $params['_query']       = "q=".Mage::helper('catalogsearch')->getQuery()->getQueryText();
        $params['_escape']      = true;
        
        return Mage::getUrl('*/*/*', $params );
    }

    protected function _prepareLayout() {
        // $_hlp = $this->_helper;
        // Return an object of current category
        $category = Mage::registry('current_category');

        if ($category) {
            $currentCategoryID = $category->getId();
        } else {
            $currentCategoryID = null;
        }

        // Return session object
        $sessionObject = Mage::getSingleton('catalog/session');
        if ($sessionObject AND $lastCategoryID = $sessionObject->getLastCatgeoryID()) {
            if ($currentCategoryID != $lastCategoryID) {
                Mage::register('new_category', true);
            }
        }
        $sessionObject->setLastCatgeoryID($currentCategoryID);

        //Create Category Blocks    
        $stateBlock = $this->getLayout()->createBlock($this->_stateBlockName)
        ->setLayer($this->getLayer());

        $categoryBlock = $this->getLayout()->createBlock($this->_categoryBlockName)
        ->setLayer($this->getLayer())
        ->init();


        // $this->setChild('layer_state', $stateBlock);
        $this->setChild('category_filter', $categoryBlock);
        // $this->createCategoriesBlock();

        // preload setting    
        // $this->setIsRemoveLinks($_hlp->removeLinks());

        //Get $this->_getFilterableAttributes() Mage_Catalog_Block_Layer_View
        $filterableAttributes = $this->_getFilterableAttributes();


        $blocks = array();
        foreach ($filterableAttributes as $attribute) {

          $blockType = $this->_attributeFilterBlockName; 

          if ($attribute->getAttributeCode() == 'price') {
              $blockType = $this->_priceFilterBlockName; 
          }

          $name = $attribute->getAttributeCode() . '_filter';

          $blocks[$name] = $this->getLayout()->createBlock($blockType)
          ->setLayer($this->getLayer())
          ->setAttributeModel($attribute);

          $this->setChild($name, $blocks[$name]);
      }

      foreach ($blocks as $name => $block) {
        $block->init();
    }
    $this->getLayer()->apply();
    return Mage_Core_Block_Template::_prepareLayout();
}


public function getLayer() {
  if (!Mage::helper('tsearch')->isTagalysActive()) {
      return Mage::getSingleton('catalogsearch/layer');
  }
  return parent::getLayer();
}

protected function createCategoriesBlock() {



  $categoryBlock = $this->getLayout()
  ->createBlock("tsearch/catalog_layer_filter_category")
  ->setLayer($this->getLayer())
  ->init();
  $this->setChild('category_filter', $categoryBlock);
}

public function getFilters() {
    if (is_null($this->_filterBlocks)) {
        $this->_filterBlocks = parent::getFilters();
    }
    return $this->_filterBlocks;
}

protected function _toHtml() {
    $html = parent::_toHtml();
    if (!Mage::app()->getRequest()->isXmlHttpRequest()) {
        $html = '<div id="catalog-filters">' . $html . '</div>';
    }
    return $html;
}
protected function _getFilterableAttributes(){
    if (Mage::helper('tsearch')->isTagalysActive()) {
    $attributeModel =  Mage::getResourceModel('catalog/product_attribute_collection')->getItems();
    return $attributeModel;
    }
    return parent::_getFilterableAttributes();
}

}

