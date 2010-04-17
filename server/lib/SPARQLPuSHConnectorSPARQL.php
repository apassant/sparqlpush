<?php

class SPARQLPuSHConnectorSPARQL extends SPARQLPuSHConnector {
	
	var $endpoint;
	
	// Nothing here - should go in subclasses
	function __construct() {
		$this->endpoint = SPARQL_ENDPOINT;
		$this->json = SPARQL_JSON;
		$this->key = SPARQL_KEY;
		$this->query = SPARQL_QUERY;
	}
	
	// Subclasses must actually RUN the query
	public function query($query) {
		$post = $this->json.'&'.$this->key.'&'.$this->query.urlencode(parent::query($query));
		$data = json_decode($this->curl_post($this->endpoint, $post));
		// Translate the results in 2 PHP arrays
		$vars = $data->head->vars;
		$bindings = $data->results->bindings;
		$results = array();
		if($data->results->bindings) {
			foreach($data->results->bindings as $b) {
				$item = array();
				foreach($vars as $var) {
					$item[$var] = $b->$var->value;
				}
				$results[] = $item;
			}
			return array('vars' => $vars, 'rows' => $results);
		}
	}
	
	private function curl_post($url, $post) {
		$ch = curl_init(POSTURL);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$response = curl_exec($ch);
		curl_close($ch);
		return $response;
	}
}