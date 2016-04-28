<?php
	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");


	// gather ----------
	@$name = $_POST['name'];
	@$email = $_POST['email'];
	@$phone = $_POST['phone'];
	@$concelho = $_POST['concelho'];
	@$freguesia = $_POST['freguesia'];
	@$codigo_postal = $_POST['codigo_postal'];
	@$isEdit = $_POST['isEdit'];

	
	// init accessories ----------
	$flag = "";
	$permissao = 0;
	$ferias = 0;
	$id = -1;


	// data treatment ----------
	if(!$name || $name==""){
		return false;
		die();
	}

	if(!$phone || $phone=="") 
		$phone='NULL';

	if($isEdit!="false"){
		$id=$isEdit;
		$isEdit=true;
		
	}else{
		$isEdit=false;
	}

	// insert of edit person --------------
	
		// edit person --------------
		$q = $conDB->sql_query("UPDATE people 
				SET name='$name', email='$email', phone='$phone', concelho='$concelho', freguesia='$freguesia', codigo_postal='$codigo_postal'
				WHERE  id_people='$id'", @BEGIN_TRANSACTION);
	
	
	
	if(@$_POST["permissao"]){
		$q = $conDB->sql_query("UPDATE people SET permissao='1' WHERE id_people='$id'");
		$permissao = 1;
	}else{
		$q = $conDB->sql_query("UPDATE people SET permissao='0' WHERE id_people='$id'");
		$permissao = 0;
	}
	
	if(@$_POST["ferias"]){
		$q = $conDB->sql_query("UPDATE people SET ferias='1' WHERE id_people='$id'");
		$ferias = 1;
	}else{
		$q = $conDB->sql_query("UPDATE people SET ferias='0' WHERE id_people='$id'");
		$ferias = 0;
	}
		
	if(@$_POST["a"]){
		
		$flag .="a";
	}
	if(@$_POST["c"]){
		
		$flag .=(($flag=="a")?"c":"c");
	}
	if(@$_POST["p"]){
		
		$flag .=(($flag=="ac")?"p":"p");
	}
	
	$q = $conDB->sql_query("UPDATE people SET flag='$flag' WHERE id_people='$id'");
	
	$q = $conDB->sql_query("",@END_TRANSACTION);
	
	$_POST['id'] = $id;
	$_POST['permissao'] = $permissao;
	$_POST['flag'] = $flag;
	$_POST['ferias'] = $ferias;

	if($q)
		echo json_encode($_POST);

?>
