<?php

	require_once("../storescripts/connectMysql.php");

	require_once("../includes/functions.php");
	
	$idp=$_POST['id'];

	$q = $conDB->sql_query("SELECT * FROM products WHERE id='$idp'");

	while($r = $conDB->sql_fetchrow($q)) {
		
		$return[] = $r;
	}

		echo json_encode($return);
	

	

?>