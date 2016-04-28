<?php
	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");

	if(!@$_POST["id_info"]) die();

	@$id_info = $_POST["id_info"];

	$q = $conDB->sql_query("DELETE FROM info WHERE id_info = $id_info",@BEGIN_TRANSACTION);
	
	//$q = $conDB->sql_query("INSERT INTO flags (pessoaID, flag) VALUES ($id,'D')");

	$q = $conDB->sql_query("",@END_TRANSACTION);

	if($q)
		echo json_encode($_POST);

?>
