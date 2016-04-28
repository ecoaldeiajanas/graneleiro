<?php

	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");
	
	$i=0;

	$q = $conDB->sql_query("SELECT encomenda.id_encomenda, people.name, encomenda.total, encomenda.date, encomenda.semana  FROM encomenda, people WHERE encomenda.id_people=people.id_people AND encomenda.semana=1" );

	while($r = $conDB->sql_fetchrow($q)) {
		foreach ($r as $key => $value) {
			$i++;
			if($i==3){ $value=number_format($value+($value*10/100),2)." - Euros";}
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
