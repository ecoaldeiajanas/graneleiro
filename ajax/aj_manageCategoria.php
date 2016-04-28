<?php
	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");


	// gather ----------
	@$category = $_POST['category'];
	@$isEdit = $_POST['isEdit'];
	
	
	
	// init accessories ----------
	$id = -1;


	// data treatment ----------
	if(!$category || $category==""){
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
		
			$q = $conDB->sql_query("UPDATE category 
				SET category='$category'
				WHERE  id_category=$id", @BEGIN_TRANSACTION);
		
	}else{
		 //add product ------------------
		
			$q = $conDB->sql_query("INSERT INTO category (category) VALUES ('$category')", @BEGIN_TRANSACTION);
			$id_category= $conDB->sql_nextid();
			
		
		
	
	}

	
	$q = $conDB->sql_query("",@END_TRANSACTION);
	


	if($q)
		echo json_encode($_POST);

?>
