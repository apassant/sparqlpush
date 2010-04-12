<?php

class SPARQLPuSHFeeds {

	var $connector;
	var $vars;
	var $rows;
	var $rss;
	var $query;
	var $time;
	
	function __construct($connector, $vars, $rows, $url, $query, $time) {
		$this->connector = strtolower($connector);
		$this->vars = $vars;
		$this->rows = $rows;
		$this->url = $url;
		$this->query = $query;
		$this->time = $time;		
	}
	
	public function make_feed() {
		if($this->connector == 'rss2') {
			return $this->rss2();
		} elseif($this->connector == 'atom') {
			return $this->atom();
		}
	}
	
	// RSS 1.0 feed
	private function rss1() {
		$date = date('c', $time);
		// Generate the items
		if($rows) {
			foreach($rows as $row) {
				$uri = $row['uri'];
				$head .= "\n\t\t<rdf:li rdf:resource=\"$uri\" />";
				$items .= "<item rdf:about=\"$uri\">";
				// Title
				if(array_key_exists('title', $vars)) {
					$items .= "\n\t<title>".$row['title'].'</title>';
				} else {
					$items .= "\n\t<title>$uri</title>";
				}		
				// Description		
				if(array_key_exists('description', $vars)) {
					$items .= "\n\t<description>".$row['description'].'</description>';
				} else {
					$items .= "\n\t<description>$uri</description>";
				}		
				// Date
				$items .= "\n\t<dc:date>".$row['date'].'</dc:date>';
				// Author
				if(array_key_exists('author', $vars)) {
					$items .= "\n\t<dc:creator>".$row['author'].'</dc:creator>';
				}
				$items .= "\n</item>";
			}
		}
		// Generate the feed
		$feed = "<?xml version=\"1.0\"?>
<rdf:RDF 
	xmlns=\"http://purl.org/rss/1.0/\"
	xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"
	xmlns:dc=\"http://purl.org/dc/elements/1.1/\"
	xmlns:atom=\"http://www.w3.org/2005/Atom\"
>

<channel rdf:about=\"$rss\">
	<title>sparqlPuSH @ ".BASENAME."</title>
	<link>$rss</link>
	<description>SPARQL results for \"$query\"</description>
	<dc:date>$date</dc:date>
	<linkrel:hub xmlns:linkrel=\"http://www.iana.org/assignments/link-relations/\" rdf:resource=\"".PUSH_HUB."\"/>
	<items>
		<rdf:Seq>$head
		</rdf:Seq>
	</items>
</channel>
$items
</rdf:RDF>";
		return $feed;
	}
	
	// Atom feed
	private function atom() {
		$date = date('c', $this->time);
		// Generate the items
		if($this->rows) {
			foreach($this->rows as $row) {
				$uri = $row['uri'];
				$items .= "\n\t<entry>";
				$items .= "\n\t\t<id>$uri</id>";
				// Title
				if(array_key_exists('title', $this->vars)) {
					$items .= "\n\t\t<title type=\"text\">".$row['title'].'</title>';
				} else {
					$items .= "\n\t\t<title type=\"text\">$uri</title>";
				}		
				// Description		
				if(array_key_exists('description', $this->vars)) {
					$items .= "\n\t\t<content type=\"text\">".str_replace('&', '&amp;', $row['description']).'</content>';
				} else {
					$items .= "\n\t\t<content type=\"text\">$uri</content>";
				}		
				// Date
				$items .= "\n\t\t<published>".date('c', strtotime($row['date'])).'</published>';
				$items .= "\n\t\t<updated>".date('c', strtotime($row['date'])).'</updated>';
				// Author
				if(array_key_exists('author', $this->vars)) {
					$items .= "\n\t\t<author>\n\t\t\t<name>".$row['author']."</name>\n\t\t</author>";
				}
				$items .= "\n\t</entry>";
			}
		}
		// Generate the feed
		$feed = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<feed xmlns=\"http://www.w3.org/2005/Atom\">
	<title type=\"text\">sparqlPuSH @ ".BASENAME."</title>
	<id>".$this->url."</id>
	<updated>".$date."</updated>
	<author>
		<name>http://code.google.com/p/sparqlpush/</name>
	</author>
	<link rel=\"self\" href=\"".$this->url."\" title=\"sparqlPuSH @ ".BASENAME."\" type=\"application/atom+xml\"/>
	<link rel=\"hub\" href=\"".PUSH_HUB."\"/>
$items
</feed>";
		return $feed;
	}
	
	// RSS 2.0 feed
	private function rss2() {
		$date = date('r', $this->time);
		// Generate the items
		if($this->rows) {
			foreach($this->rows as $row) {
				$uri = $row['uri'];
				$items .= "\n\t<item>";
				// Title
				if(array_key_exists('title', $this->vars)) {
					$items .= "\n\t\t<title>".$row['title'].'</title>';
				} else {
					$items .= "\n\t\t<title>$uri</title>";
				}		
				// Description		
				if(array_key_exists('description', $this->vars)) {
					$items .= "\n\t\t<description>".$row['description'].'</description>';
				} else {
					$items .= "\n\t\t<description>$uri</description>";
				}		
				// Date
				$items .= "\n\t\t<pubDate>".date('r', strtotime($row['date'])).'</pubDate>';
				// Author
				if(array_key_exists('author', $this->vars)) {
					$items .= "\n\t\t<author>".$row['author'].'</author>';
				}
				$items .= "\n\t</item>";
			}
		}
		// Generate the feed
		$feed = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<rss version=\"2.0\"
	xmlns:atom=\"http://www.w3.org/2005/Atom\"
>

<channel>
	<title>sparqlPuSH @ ".BASENAME."</title>
	<link>".$this->url."</link>
	<description>SPARQL results for \"".$this->query."\"</description>
	<lastBuildDate>".$this->date."</lastBuildDate>
	<generator>http://code.google.com/p/sparqlpush/</generator>
	<atom:link rel=\"hub\" href=\"".PUSH_HUB."\"/>
$items
</channel>

</rss>";
		return $feed;
	}
}
