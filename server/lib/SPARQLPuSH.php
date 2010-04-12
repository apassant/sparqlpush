<?php

include_once('./arc/ARC2.php');

include_once(dirname(__FILE__).'/Publisher.php');
include_once(dirname(__FILE__).'/SPARQLPuSHFeeds.php');
include_once(dirname(__FILE__).'/SPARQLPuSHTemplate.php');
include_once(dirname(__FILE__).'/SPARQLPuSHConnectorARC2.php');

class SPARQLPuSH {
	
	var $connector;
	
	public function __construct() {
		if(CONNECTOR == 'ARC2') {
			$this->connector = new SPARQLPuSHConnectorARC2();
		} elseif (CONNECTOR == 'SPARQL') {
			$this->connector = new SPARQLPuSHConnectorSPARQL();
		}
	}
	
	// Main method
	public function go() {
		parse_str($_SERVER['QUERY_STRING']);

		// Get / generate a feed
		if($query) return $this->feed(trim($query));
		// List all feeds
		elseif($list) {
			SPARQLPuSHTemplate::render($this->list_feeds(), 'Available feeds');
		} 
		// SPARQL/Update query + feeds update
		elseif($post) {
			if($_POST['query']) {
				$this->connector->query($_POST['query']);
				$this->update_feeds();
			} else {
				SPARQLPuSHTemplate::render(SPARQLPuSHTemplate::post(), 'sparqlPuSH SPARQL/Update interface');
			}
		}
		// Home
		else {
			SPARQLPuSHTemplate::render(SPARQLPuSHTemplate::form(), 'sparqlPuSH home');
		}
	}
		
	//  Generate the RSS feed for a query
	private function feed($query) {
		// It seems that some RDF store do not like \n in the text literals
		$enc = urlencode($query);
		// Check if the query is already registered
		$select = "SELECT ?feed WHERE { GRAPH <".BASENAME."/feeds> { ?feed <http://ex/has_query> \"$enc\" } }";
		$res = $this->connector->query($select);
		$rows = $res['rows'];
		// If not - create a feed and register the query
		if(!count($rows)) {
			$register = $this->register($query);
			if(!$register) {
				// TODO - Return HTTP error code
				echo "Error: Please ensure that you use ?uri and ?date in your SPARQL query";
				die();
			}
			return $this->feed($query);
		}
		// Redirect to the feed URI
		header("Content-Type: application/rdf+xml");
		header('Location: '. $rows['0']['feed']);
	}

	// Register a query
	private function register($query) {
		$enc = urlencode($query);
		$id = uniqid();
		// Create the feed
		$time = time();
		$rss = $this->make_feed($query, $id, $time);		
		// if OK
		if($rss) {
			// Subscribe
			$this->push($rss);
			// Register
			$date = date('c', $time);
			$insert = "INSERT INTO <".BASENAME."/feeds> { <$rss> <http://ex/has_query> \"$enc\" ; dc:date \"$date\" . } ";
			return $this->connector->query($insert);
		} else {
			return null;
		} 
	}
	
	// Get feeds
	private function get_feeds() {
		$select = "SELECT DISTINCT ?feed ?query ?date WHERE { GRAPH <".BASENAME."/feeds> { ?feed <http://ex/has_query> ?query ; dc:date ?date } }";
		$res = $this->connector->query($select);
		return $res['rows'];
	}

	// List feeds
	private function list_feeds() {
		// Retrieve feeds
		$rows = $this->get_feeds();
		if($rows) {
			$data = "<dl>";
			foreach($rows as $row) {
				$feed = $row['feed'];
				$date = $row['date'];
				$query = htmlentities(urldecode($row['query']));
				if($query) {
					$data .= "<dt><b>Feed:</b> <a href='$feed'>$feed</a> ($date)</dt><dd><pre>$query</pre></dd>";
				}
			}
		} else {
			$data ='<p>No query registered yet.</p>';
		}
		$data .= "</dl>";
		return $data;
	}

	// Update all RSS feeds 
	private function update_feeds() {
		// Retrieve feeds
		$rows = $this->get_feeds();
		if($rows) {
			$time = time();
			foreach($rows as $row) {
				$feed = $row['feed'];
				$checkdate = $row['date'];
				$query = urldecode($row['query']);
				$id = array_pop(explode('/', $feed));
				$rss = $this->make_feed($query, $id, $time, $checkdate);
				// Updated ?
				if($rss) {
					// Update the date
					$delete = "DELETE FROM <".BASENAME."/feeds> { <$rss> dc:date ?old . } ";
					$this->connector->query($delete);
					$date = date('c', $time);
					$insert = "INSERT INTO <".BASENAME."/feeds> { <$rss> dc:date \"$date\" . } ";
					$this->connector->query($insert);
					// Notify the PuSH hub server
					$this->push($rss);
				}
			}
		}
	}
	
        // Notify a PuSH hub of an updated feed
        private function push($rss) {
                $p = new Publisher(PUSH_HUB_PUBLISH);
                $res = $p->publish_update($rss);
                if($res) {
                        echo "<br/>Successfully published $rss to " . PUSH_HUB_PUBLISH;
                }
        }
	
	private function make_feed($query, $id, $time, $checkdate = null) {
		// Feed URL
		$url = BASENAME."/feed/$id";
		// Run the query
		$res = $this->connector->query($query);
		$vars = array_flip($res['vars']);
		$rows = $res['rows'];
		// ?uri and ?date must be here
		if(!array_key_exists('uri', $vars) || !array_key_exists('date', $vars) ) {
			return null;
		}
		if($rows) {
			// If that's a request for update, check if the feed needs to be updated
			if($checkdate && $rows[0]['date'] <= $checkdate) {
				return null;
			}
		}
		$feedr = new SPARQLPuSHFeeds(FEED, $vars, $rows, $url, $query, $time);
		$feed = $feedr->make_feed();
		// Save
		$file = fopen(dirname(__FILE__)."/../feed/$id", 'w');
		fwrite($file, $feed);
		fclose($file);
		return $url;
	}
}
