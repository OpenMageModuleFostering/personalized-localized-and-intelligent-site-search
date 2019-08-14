<?php
/**
 * Similar Products Helper 
 */
class Tagalys_SimilarProducts_Helper_Data extends Mage_Core_Helper_Abstract {
    public function isTagalysActive() {
        // die('active');
        $status =  Mage::helper('tagalys_core')->getTagalysConfig("is_similar_products_active");
        //$query = Mage::app()->getRequest()->getParam('q');
        if ($status) {
          $service = Mage::getSingleton("similarproducts/client");
          //$tagalys = $service->isRequestSuccess();
          if($service) {
            return true;
          } else {
            return false;
          }
        } else {
          return false;
        }
        // return true;
    }
}
