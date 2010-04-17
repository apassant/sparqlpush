<?php

include_once('./arc/ARC2.php');

class SPARQLPuSHConnectorARC2 extends SPARQLPuSHConnector {
	
	var $store;
	
	function __construct() {
		$config = array(
			'db_host' => DB_HOST,
			'db_name' => DB_NAME,
			'db_user' => DB_USER,
			'db_pwd' => DB_PASS,
			'store_name' => DB_STORE,
		);
		$this->store = ARC2::getStore($config);
		if (!$this->store->isSetUp()) {
			$this->store->setUp();
		}
	}
	
	public function query($query) {
		$res = $this->store->query(parent::query($query));
		return array('vars' => $res['result']['variables'], 'rows' => $res['result']['rows']);
	}
	
}