<!DOCTYPE html>
<html>
<head>
	<title>123</title>
	<script type="text/javascript" src="<?php echo STYLE_PATH; ?>/js/jquery-3.3.1.js"></script>
</head>
<body>
	<div id = 'result'>
		abc
	</div>
<script type="text/javascript">
	
var source=new EventSource("/bar/message");
source.onmessage=function(event){
	$("#result").html($("#result").html()+event.data + "<br />");
	alert(1);
};
</script>

</body>
</html>