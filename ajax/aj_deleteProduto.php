<?php
	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");

	if(!@$_POST["id_produto"]) die();

	@$id_produto = $_POST["id_produto"];

	$q = $conDB->sql_query("DELETE FROM products WHERE id = $id_produto",@BEGIN_TRANSACTION);
	
	$q = $conDB->sql_query("",@END_TRANSACTION);

	if($q)
		echo json_encode($_POST);

?>
