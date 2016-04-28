<?php header('Content-type: text/html; charset=utf-8');?>
<!DOCTYPE html>
<meta http-equiv="content-type" content="text/html charset=UTF-8"/>
<html>
<head>

<link href="style/css/reset.css" rel="stylesheet" type="text/css"/>
<link href="style/css/jqui/jquery-ui-1.8.20.custom.css" rel="stylesheet" type="text/css"/>
<link href="style/css/style.css" rel="stylesheet" type="text/css"/>
<? 
	$time = time();
	foreach (glob("style/css/*.css") as $filename)
	{
	    echo "<link href='$filename?$time' rel='stylesheet' type='text/css'/>";
	} 
?>

<script type="text/javascript" src="js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="js/jquery-ui-1.8.20.custom.min.js"></script>
<? 
	foreach (glob("js/s1/*.js") as $filename)
	{
	    echo "<script type='text/javascript' src='$filename'></script>";
	}
	foreach (glob("js/s2/*.js") as $filename)
	{
	    echo "<script type='text/javascript' src='$filename'></script>";
	} 
?>



</head>
<body>