<?php

class Tagalys_SimilarProducts_Model_Client extends Mage_Core_Model_Abstract {

	protected $_api_key;
	protected $_api_server;
	
	protected $_search = array();

	protected $_error = false;

	protected $timeout = 5;

	protected $visitor;


	public function getSearchResult() {		
		if(!empty($this->_search)) {
			return $this->_search;
		} 
		return $this->_search;
	}

	protected function _construct() {
		$this->_config = Mage::helper('tagalys_core');
		$this->_api_server = $this->_config->getTagalysConfig("api_server");
		$this->_api_key = $this->_config->getTagalysConfig("private_api_key");
		$this->_client_code =  $this->_config->getTagalysConfig("client_code");
		
		$this->_similar_products_ep = Mage::getStoreConfig('tagalys/endpoint/similarproducts');
	}


	protected function createPayload($payload , $action) {
		$request = array(
				'client_code' => $this->_client_code,
				'api_key' => $this->_api_key,
				);
		$payload["identification"] = $request;

		return json_encode($payload);
	}

	
	public function similarProducts($product_id, $limit) {
		//var_dump($product_id);
		try {
			
				$rawurl = $this->_api_server.$this->_similar_products_ep;

				$url  = str_replace(":id", $product_id, $rawurl);
				$payload['api_key'] = $this->_api_key;
				$payload['product_id'] = $product_id;
				$payload['per_page'] = $limit;

				$this->_search = $this->_payloadAgent($url, $this->createPayload($payload));
				//$this->_search = json_decode(file_get_contents('similar_products.json'),true);
				//die(var_dump($this->_search));
				return $this->_search;
		} catch (Exception $e) {
			
		}
	
	}

	private function _getAgent($url) {
		$agent = curl_init($url);
		return $agent;
	}

	private function _queryAgent($url, $query) {
		$q_url = $url;
		$q_url .= '?q='.$query;
		$q_url .= '&api_key='.$this->_api_key;
		$agent = $this->_getAgent($url);
		curl_setopt($agent, CURLOPT_SSL_VERIFYPEER, 0); 
		curl_setopt( $agent, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($agent, CURLOPT_TIMEOUT, $this->timeout); 

		$result = curl_exec($agent);
		$info = curl_getinfo($agent);

		if(curl_errno($agent)) {
		
			if (curl_error($agent) === "name lookup timed out") {
				for($i = 0; $i <=2 ; $i++) {
					$this->_queryAgent($url, $query);
				}
			}
		} else {
			if (empty($result)) {
				$this->_error = false;
		
			}
			return $result;
		}
	//end of curl error log
		curl_close($agent);
	}

	private function _payloadAgent($url, $payload) {

		$agent = $this->_getAgent($url);
		curl_setopt( $agent, CURLOPT_POSTFIELDS, $payload );
		curl_setopt($agent, CURLOPT_POST,1);
		curl_setopt( $agent, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		curl_setopt( $agent, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $agent, CURLOPT_SSL_VERIFYPEER, 0 );
		curl_setopt($agent, CURLOPT_TIMEOUT, $this->timeout);
		$result = curl_exec($agent);
		$info = curl_getinfo($agent);

				Mage::log("Tagalys Request info: ".json_encode($info),null,'request.log');
		Mage::log("Tagalys Request payload: ".($payload),null,'request.log');
		Mage::log("Tagalys Response: ".($result),null,'request.log');
	

		if(curl_errno($agent)) {
			$this->_error = true;
		
		} else {
			if (empty($result)) {
				$this->_error = true;
	
			} 
		}
		curl_close($agent);
		if (!$this->_error) {
			$decoded = json_decode($result, true);
			return $decoded;
		} else {
			return null;
		}
	}

	public function isRequestSuccess() {
	
		if( $this->_error == true || empty($this->_search) || $this->_search["status"] != "OK" ) {
			return false; //ref addy
		}
		return true;
	}
}
