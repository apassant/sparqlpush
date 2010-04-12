<?php

require_once("./subscriber.php");
require_once("./config.php");
require_once('./simplepie/simplepie.inc');

parse_str($_SERVER['QUERY_STRING']);

// If there's a query, let's subscribe
if($query) {
	// Get the feed
	$url = $endpoint.'?query='.urlencode($query);
	$data = file_get_contents($url);
	$feed = new SimplePie();
	$feed->set_raw_data($data);
	$feed->init();
	// Retrieve info
	$hubs = $feed->get_links('hub');
	$hub = $hubs[0];
	$urls = $feed->get_links('self');
	$url = $urls[0];
	// Subscribe
	$s = new Subscriber($hub.'subscribe', BASENAME."/endpoint.php");
	$s->subscribe($url);

// Display the form
} else {
	echo "
<form action='register.php'>
Query:

<textarea name=\"query\" rows=\"10\" cols=\"80\">
SELECT ?uri ?author ?title ?date ?description
WHERE {
  ?uri a sioct:MicroblogPost ;
    sioc:has_creator ?author ;
    sioc:content ?description ;
    dct:title ?title ;
    dct:created ?date .	
} ORDER BY DESC(?date)
</textarea>

<br/>

sparqlPuSH interface:
<input name=\"endpoint\" size=\"80\"/>

<br/>

<input type=\"submit\" value=\"Register\"/>
</form>
";

}

?>
