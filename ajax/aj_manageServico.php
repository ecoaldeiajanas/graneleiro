<?php
	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");


	// gather ----------
	@$servico = $_POST['servico'];
	@$nome = $_POST['nome'];
	@$telef = $_POST['telef'];
	@$email = $_POST['email'];
	@$obs = $_POST['obs'];
	$obs = trim($obs);
	$obs = str_replace("\"", "", $obs);
	$obs = str_replace("\r", "<br />", $obs);
	$obs = str_replace("\n", "", $obs);
	
	@$imagem = $_POST['fileName'];
	@$isEdit = $_POST['isEdit'];

	// init accessories ----------
	$id = -1;
	

	// print_r($_POST);

	// data treatment ----------
	if(!$servico || $servico==""){
		return false;
		die();
	}

	if($isEdit!="false"){
		$id=$isEdit;
		$isEdit=true;
	}else{
		$isEdit=false;
	}

	// insert or edit servico --------------
	if($isEdit){
		// edit servico --------------
		if($imagem && $imagem!="")
			$q = $conDB->sql_query("UPDATE servicos 
				SET servico='$servico',imagem='$imagem', nome='$nome', telef='$telef', email='$email', obs='$obs' 
				WHERE  id_servico=$id", @BEGIN_TRANSACTION);
		else
			$q = $conDB->sql_query("UPDATE servicos 
				SET servico='$servico', nome='$nome', telef='$telef', email='$email', obs='$obs' 
				WHERE  id_servico=$id", @BEGIN_TRANSACTION);
	}else{
		 //add servico ------------------
		if($imagem && $imagem!="")
			$q = $conDB->sql_query("INSERT INTO servicos (servico, nome, telef, email, obs, imagem) VALUES ('$servico','$nome','$telef','$email','$obs','$imagem')", @BEGIN_TRANSACTION);
		else
			$q = $conDB->sql_query("INSERT INTO servicos (servico, nome, telef, email, obs) VALUES ('$servico','$nome','$telef','$email','$obs')", @BEGIN_TRANSACTION);
		$id_servico= $conDB->sql_nextid();
	}

	
	
	
	if(!$q){
		$r = $conDB->sql_error($q);
		echo $r["code"];
	}
	
	$q = $conDB->sql_query("",@END_TRANSACTION);
	
	if(!$q) die();

	$_POST['id'] = $id;


	if($q)
		echo json_encode($_POST);

?>
