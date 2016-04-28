<?php

	require_once("../storescripts/connectMysql.php");

	require_once("../includes/functions.php");

	$q = $conDB->sql_query("SELECT id_category, category FROM category ORDER BY id_category ASC");

	while($r = $conDB->sql_fetchrow($q)) {
		@$return[] = $r;
	}
//echo "teste".$return[0]; exit();
	if(@$_GET['normalJSON'])
		echo json_encode($return);
	else
		echo utf8_decode(json_encode_dataTable($return));

	

?>
