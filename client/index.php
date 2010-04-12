<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML+RDFa 1.0//EN" 
 	"http://www.w3.org/MarkUp/DTD/xhtml-rdfa-1.dtd">
<html
	xmlns="http://www.w3.org/1999/xhtml" 
	xml:lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
  <title>sparqlPuSH demo client</title>
  <script type="text/javascript" src="http://www.google.com/jsapi"></script>	
  <script type="text/javascript">	
    google.load("jquery", "1.4.1");
    google.load("jqueryui", "1.7.2");
  </script>
  <script type="text/javascript" src="./jquery.timers-1.2.js"></script>
  <script type="text/javascript">
	$(function() { 
		$("#content").everyTime(1000,function(i) {
			update();
		});
	});
	function update() {
		var np = $('#content').html(); 
		$.get("parse.php", function(data) {
			if(data) {
				$('#content').html(data);	
			}
		});
	}
  </script>
  </head>

<body>

<h1>sparqlPuSH demo client: Updates</h1>

<div id="content">
</div>

</body>

</html>