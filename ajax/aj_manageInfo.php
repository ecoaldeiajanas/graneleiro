<?php
	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");


	// gather ----------
	@$titulo = $_POST['titulo'];
	@$texto = $_POST['texto'];
	$texto = trim($texto);
	$texto = str_replace("\"", "", $texto);
	$texto = str_replace("\r", "<br />", $texto);
	$texto = str_replace("\n", "", $texto);

	@$isEdit = $_POST['isEdit'];
	
	// init accessories ----------
	$id = -1;


	// data treatment ----------
	if(!$titulo || $titulo==""){
		return false;
		die();
	}
	
	if($isEdit!="false"){
		$id=$isEdit;
		$isEdit=true;
	}else{
		$isEdit=false;
	}

	// insert or edit product --------------
	if($isEdit){
		// edit product --------------
		
			$q = $conDB->sql_query("UPDATE info 
				SET titulo='$titulo', texto='$texto'
				WHERE  id_info=$id", @BEGIN_TRANSACTION);
		
	}else{
		 //add product ------------------
		
			$q = $conDB->sql_query("INSERT INTO info (titulo,texto,data) VALUES ('$titulo','$texto',now())", @BEGIN_TRANSACTION);
			$id_category= $conDB->sql_nextid();
			
		
		
	
	}

	
	$q = $conDB->sql_query("",@END_TRANSACTION);
	


	if($q)
		echo json_encode($_POST);

?>
