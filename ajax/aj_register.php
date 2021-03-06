<?php

	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");

	$name 		= mysql_real_escape_string($_POST['name']);
	$email 		= mysql_real_escape_string($_POST['email']);
	$localidade	= mysql_real_escape_string($_POST['localidade']);
	$contacto	= mysql_real_escape_string($_POST['contacto']);
	$pass1 		= md5($_POST['pass1']);
	$pass 		= md5($_POST['pass']);
	$flag		="c";

	if(!$email || $email==""){
		$rs["SUCCESS"]="NO";
		$rs["REASON"]="NO_EMAIL";
		echo json_encode($rs);
		die();
	}

	if(!$name || $name==""){
		$rs["SUCCESS"]="NO";
		$rs["REASON"]="NO_NAME";
		echo json_encode($rs);
		die();
	}
	
	if(!$localidade|| $localidade==""){
		$rs["SUCCESS"]="NO";
		$rs["REASON"]="NO_LOCAL";
		echo json_encode($rs);
		die();
	}
	
	if(!$contacto || $contacto ==""){
		$rs["SUCCESS"]="NO";
		$rs["REASON"]="NO_CONTACTO";
		echo json_encode($rs);
		die();
	}

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

	if (!preg_match('|^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$|i', $email)) {
		$rs["SUCCESS"]="NO";
		$rs["REASON"]="NOT_EMAIL";
		echo json_encode($rs);
		die();
	}

	// verificar se o email já existe
	$q = $conDB->sql_query("SELECT email FROM people WHERE email = '$email'");
	if($conDB->sql_numrows($q)>0){
		$rs["SUCCESS"]="NO";
		$rs["REASON"]="EMAIL_EXISTS";
		echo json_encode($rs);
		die();
	}

	// inserir
	$q = $conDB->sql_query("INSERT INTO people (name, password, email, freguesia, phone, flag) VALUES ('$name','$pass','$email', '$localidade', '$contacto', '$flag')");
	if(!$q){
		$rs["SUCCESS"]="NO";
		$rs["REASON"]="generico";
		echo json_encode($rs);
		die();
	}
	
	$id= $conDB->sql_nextid();
		
	$rs["SUCCESS"]="YES";
	$rs["email"]=$email;
	$rs["name"]=$name;	
	echo json_encode($rs);
