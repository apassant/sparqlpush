<?php

class SPARQLPuSHConnector {
	
	// Nothing here - should go in subclasses
	function __construct() {
	}
	
	// Subclasses must actually RUN the query
	public function query($query) {
		$query = "
PREFIX sioc: <http://rdfs.org/sioc/ns#>
PREFIX sioct: <http://rdfs.org/sioc/types#>
PREFIX foaf: <http://xmlns.com/foaf/0.1/>
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX dc: <http://purl.org/dc/elements/1.1/>
PREFIX dct: <http://purl.org/dc/terms/>
PREFIX tags: <http://www.holygoat.co.uk/owl/redwood/0.1/tags/>
PREFIX moat: <http://moat-project.org/ns#>
PREFIX opo: <http://online-presence.net/opo/ns#>
PREFIX opo-actions: <http://online-presence.net/opo-actions/ns#>
PREFIX ctag: <http://commontag.org/ns#>
PREFIX smob: <http://smob.me/ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
PREFIX rev: <http://purl.org/stuff/rev#>
PREFIX geo: <http://www.w3.org/2003/01/geo/wgs84_pos#>
$query
";
		return $query;
	}
	
}