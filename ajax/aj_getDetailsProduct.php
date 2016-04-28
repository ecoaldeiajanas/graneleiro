<?php
	require_once("../storescripts/connectMysql.php");

	require_once("../includes/functions.php");

	@$targetID=$_GET['id'];
	$i=0;

	$q = $conDB->sql_query("SELECT id_produto, name, quant, semana FROM encomenda_has_products, people, encomenda WHERE encomenda.id_encomenda=encomenda_has_products.id_encomenda 
AND encomenda.id_encomenda=encomenda_has_products.id_encomenda
AND encomenda.semana=0 
AND encomenda_has_products.id_produto='$targetID' 
AND people.id_people=encomenda.id_people");

	while($r = $conDB->sql_fetchrow($q)) {
		
		foreach ($r as $key => $value) {
			
			$i++;
			if($i==1){
				$sql=mysql_query("SELECT * FROM products WHERE id='$value'");
					while($row=mysql_fetch_array($sql)){
						$unit=$row['unit'];		
						}
							if($unit==1){
								$unid=" Unid.";
							}else{
								$unid=" Kg";
							}
			}
			if($i==3){ $value=$value.$unid;}
			if(!@$_GET['normalJSON']) $r[$key] = $value;
			}
			$return[] = $r;
			$i=0;
			
		
}
	if(@$_GET['normalJSON'])
		echo json_encode(@$return);
	else
		echo utf8_decode(json_encode_dataTable(@$return));

?>
