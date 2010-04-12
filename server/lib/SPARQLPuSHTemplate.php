<?php

class SPARQLPuSHTemplate {

	public function render($data, $title) {
		SPARQLPuSHTemplate::header($title);
		echo $data;
		SPARQLPuSHTemplate::footer();
	}

	public function header($title) {
		echo '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN"
"http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"
xmlns:foaf="http://xmlns.com/foaf/0.1/"
xmlns:dc="http://purl.org/dc/elements/1.1/" 
version="XHTML+RDFa 1.0" xml:lang="en">
<head>
<title>'.$title.'</title>
<link rel="stylesheet" type="text/css" href="./style.css" />
</head>
<body>

<h1>'.$title.'</h1>

';
	}	

	public function post() {
			return '
	<form id="sparql" action="./?post=1" enctype="application/x-www-form-urlencoded" method="post">
	<textarea id="query" name="query" rows="10" cols="50"></textarea>
	<input type="submit" name="Go!"/>
	';
		}
			
	public function form() {
		return '
<p>
Type-in a SPARQL query in the following form and the results will be provided as a RSS feed, including a pubsubhubbub hub address for broadcasting updates. You should use the following conventions regarding the items you query:
<ul>
	<li><code>?uri</code>: their URI;</li>
	<li><code>?date</code>: their creation / modification date;</li>
</ul>
And you can optionally use:
<ul>
	<li><code>?label</code>: their label;</li>
	<li><code>?author</code>: their author;</li>
	<li><code>?description</code>: their description;</li>
</ul>
As an example, the default query on the right will provide you with an RSS feeds describing the last SIOC items that have been loaded in the RDF store.
<br/>
In addition, you can <a href="?list=1">check the list of available feeds</a> that you can subscribe to.
</p>

<form id="sparql" action="." enctype="application/x-www-form-urlencoded" method="get">
<textarea id="query" name="query" rows="10" cols="50">
SELECT ?uri ?author ?title ?date ?description
WHERE {
  ?uri a sioct:MicroblogPost ;
    sioc:has_creator ?author ;
    sioc:content ?description ;
    dct:title ?title ;
    dct:created ?date .	
} ORDER BY DESC(?date)
</textarea>
<input type="submit" name="Go!"/>
';
	}
	
	public function footer() {
		echo '		
<div id="footer">
	This is the <a href="http://code.google.com/p/sparqlpush">sparqlPuSH</a> interface for <a href="'.BASENAME.'">'.BASENAME.'</a>
</div>

</body>

</html>';
	}

}


?>