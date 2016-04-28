<?php

	require_once("../storescripts/connectMysql.php");

	require_once("../includes/functions.php");

	$q = $conDB->sql_query("SELECT id_people, name, email, phone, concelho, freguesia, codigo_postal, flag, permissao, ferias FROM people");

	while($r = $conDB->sql_fetchrow($q)) {
		foreach ($r as $key => $value) {
			
			if(!@$_GET['normalJSON']) $r[$key] = $value;
		}
		@$return[] = $r;
		
	}

	if(@$_GET['normalJSON'])
		echo json_encode($return);
	else
		echo utf8_decode(json_encode_dataTable($return));

	

?>
