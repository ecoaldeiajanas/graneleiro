<?php

	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");
	
	//$obs='';
	$i=0;
	$targetID=@$_GET['idEnc'];

	$q = $conDB->sql_query("SELECT products.id, products.product_name, products.obs, products.price, encomenda_has_products.quant ,products.price*encomenda_has_products.quant, semana  FROM encomenda, encomenda_has_products, products WHERE encomenda.id_people='$targetID' 
AND encomenda.id_encomenda=encomenda_has_products.id_encomenda
AND encomenda.semana=0 
AND encomenda_has_products.id_produto=products.id 
AND encomenda_has_products.id_encomenda=encomenda.id_encomenda");

	while($r = $conDB->sql_fetchrow($q)) {
		
		foreach ($r as $key => $value) {
			@$i++;
			if($i==1){
				$sql=mysql_query("SELECT * FROM products WHERE id='$value'");
					while($row=mysql_fetch_array($sql)){
						$peso=$row['peso'];
						$unit=$row['unit'];
						$obs=$row['obs'];
						}
							if($unit==1||$peso==1){
								$unid=" Unid.";
							}else{
								$unid=" Kg";
							}
							if($peso==1){
								$valueT=0;
								$obs='Necessario aferir o peso e acertar o valor.<br/>'.$obs;
							}else{
								$valueT=1;
								$obs=$obs;
							}
			}
			if($i==6){ $value=number_format($value*$valueT,2);}
			if($i==5){ $value=$value.$unid;}
			if($i==3){ $value=$obs;}
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
