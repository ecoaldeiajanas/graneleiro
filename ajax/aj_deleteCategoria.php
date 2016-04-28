<?php
	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");

	if(!@$_POST["id_category"]) die();

	@$id_category = $_POST["id_category"];

	$q = $conDB->sql_query("DELETE FROM category WHERE id_category = $id_category",@BEGIN_TRANSACTION);
	
	//$q = $conDB->sql_query("INSERT INTO flags (pessoaID, flag) VALUES ($id,'D')");

	$q = $conDB->sql_query("",@END_TRANSACTION);

	if($q)
		echo json_encode($_POST);

?>
