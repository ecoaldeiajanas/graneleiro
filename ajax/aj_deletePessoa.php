<?php
	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");

	if(!@$_POST["id_people"]) die();

	@$id_people = $_POST["id_people"];

	$q = $conDB->sql_query("DELETE FROM people WHERE id_people = $id_people",@BEGIN_TRANSACTION);
	
	//$q = $conDB->sql_query("INSERT INTO flags (pessoaID, flag) VALUES ($id,'D')");

	$q = $conDB->sql_query("",@END_TRANSACTION);

	if($q)
		echo json_encode($_POST);

?>
