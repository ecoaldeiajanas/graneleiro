<?php

	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");
	
	$i=0;
	
	$q = $conDB->sql_query("SELECT COUNT(*), SUM(total), semana FROM encomenda WHERE semana=1");	

	

	while($r = $conDB->sql_fetchrow($q)) {
		foreach ($r as $key => $value) {
			$i++;
			//if($i==1){ $value= "Transações - ".$value;}
			if($i==2){ $value=$value." - Euros";}
			if(!@$_GET['normalJSON']) $r[$key] = $value;
		}
		@$return[] = $r;
		$i=0;
	}
//echo "teste".$return[0]; exit();
	if(@$_GET['normalJSON'])
		echo json_encode($return);
	else
		echo utf8_decode(json_encode_dataTable($return));

	

?>
