<?
	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");

	
	$email 		= mysql_real_escape_string($_POST['email']);
	$pass1 		= md5($_POST['pass1']);
	$pass 		= md5($_POST['pass']);
	

	

	if(!$pass || $pass==""){
		$rs["SUCCESS"]="NO";
		$rs["REASON"]="NO_PASS";
		echo json_encode($rs);
		die();
	}
	
	if($pass1 != $pass){
		$rs["SUCCESS"]="NO";
		$rs["REASON"]="DIF_PASS";
		echo json_encode($rs);
		die();
	}
	
	if(strlen($pass)<=3){
		$rs["SUCCESS"]="NO";
		$rs["REASON"]="SHORT_PASS";
		echo json_encode($rs);
		die();
	}

	


	// actualizar
	$q = $conDB->sql_query("UPDATE people SET password ='$pass' WHERE email='$email'");
	if(!$q){
		$rs["SUCCESS"]="NO";
		$rs["REASON"]="generico";
		echo json_encode($rs);
		die();
	}
	
	
	
	$rs["SUCCESS"]="YES";
	echo json_encode($rs);
