<?php

$challenge = $_GET['hub_challenge'];

if($challenge) {
	// Echo the challenge to confirm subscription
	header('HTTP/1.1 200 "Found"', null, 200);
	print $challenge;
} else {
	// Store the feed received by th PuSH hub
	$f = fopen(dirname(__FILE__).'/feed','w');
	fwrite($f, $HTTP_RAW_POST_DATA);
	fclose($f);
}
