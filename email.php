<?php
require_once("includes/phpmailer/class.phpmailer.php");

	@$name =$_GET['name'];
	@$email=$_GET['email'];

///////////////////////////////////////////////////////////////////////////////
	//email para o Administrador
	
	$ownerEmail='geral@ecoaldeiajanas.org';
	//$array=(explode(" ",$name));
	//$firstName=$array[0];
	
	$to = $ownerEmail;
	
	$subject = 'Prossumidores - Novo registo.';
	
	$headers = "From: Prossumidores  <" . strip_tags($ownerEmail) . ">\n";
	$headers .= "Reply-To: ". strip_tags($email) . "\r\n";
	//$headers .= "CC: susan@example.com\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=utf-8\r\n";
	
	//$message = '<html><body>';
	$message  ='<p>Viva!</p>';
	$message .='<p>Foi criado um novo Registo com os seguintes dados:</br><li><b>Nome:</b> '. $name.'</li><li><b>E-mail:</b> '. $email.'</li></p>';
	//$message .='<p>Será necessário dar permissão de utilizador no painel de ADMIN/Pessoas.</p>';
	
	mail($to, $subject, $message, $headers);

	header("location:login.php");
	exit();
?>
