<?php
	require_once("../storescripts/connectMysql.php");
	//require_once("../includes/functions.php");

	$email 		= mysql_real_escape_string($_POST['email']);
	$pass		= mysql_real_escape_string($_POST['pass']);

	if(!$email || $email==""){
		$rs["e"]="No email was given";
		echo json_encode($rs);
		die();
	}
	
	

	$q = $conDB->sql_query("SELECT *  FROM people WHERE email='$email' ");
		$id= $conDB->sql_nextid();

	if(!$q){
		$r = $conDB->sql_error($q);
		$rs["e"]=$r["message"];
		die();
	}
	
	

	if($conDB->sql_numrows($q)){
		while($r = $conDB->sql_fetchrow($q)) {
			$rs['email']=$r['email'];
			$pass_DB=$r['password'];
			//$pid = $r['id'];
			//$name = $r['nome'];
			//$flags = $r['F'];
			///////
			$id=$r['id_people'];
			$email=$r['email'];
			$name=$r["name"];
			$flag=$r['flag'];
			$permissao=$r['permissao'];
	
		}
	
	}else{
		$rs["SUCCESS"]="NO";
		$rs["REASON"]="UNKNOWN_USER";
		echo json_encode($rs);
		die();
	}

	if($pass_DB=="null" || $pass_DB==null){
		$rs["SUCCESS"]="NO";
		$rs["REASON"]="NO_PASS";
		echo json_encode($rs);
		die();
	}
	if($permissao==0 || $permissao==null){
		$rs["SUCCESS"]="NO";
		$rs["REASON"]="NO_PERMISSION";
		echo json_encode($rs);
		die();
	}

	if(md5($pass) != $pass_DB){
		$rs["db"] = md5($pass) . " " . $pass_DB;
		$rs["SUCCESS"]="NO";
		$rs["REASON"]="WRONG_PASS";
		echo json_encode($rs);
		die();
	}else{
		$rs["SUCCESS"]="YES";
		$rs["flag"]=$flag;
		//$rs["permissao"]=$permissao;
		session_start();
		$_SESSION = array(); //clear
		$_SESSION["id"]=$id;
		$_SESSION["name"]=$name;
		$_SESSION["email"]=$email;
		if($flag=="cp"){
			$_SESSION["produtor"]=$email;
			$_SESSION["manager"]=false;			
		}elseif($flag=="ac"|| $flag=="acp"){
			$_SESSION["manager"]=$email;
			$_SESSION["produtor"]=false;
		}elseif($flag=="c"){
			$_SESSION["cliente"]=$email;
		}
		
		//$_SESSION["password"]=md5($pass);
		//if(strpos($flags,"C")!==false) $_SESSION["admin"] = true;
		echo json_encode($rs);
		die();
	}
?>
