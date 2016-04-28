<?php
	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");

	if(!@$_POST["id_servico"]) die();

	@$id_servico = $_POST["id_servico"];

	$q = $conDB->sql_query("DELETE FROM servicos WHERE id_servico = $id_servico",@BEGIN_TRANSACTION);
	
	//$q = $conDB->sql_query("INSERT INTO flags (pessoaID, flag) VALUES ($id,'D')");

	$q = $conDB->sql_query("",@END_TRANSACTION);

	if($q)
		echo json_encode($_POST);

?>
