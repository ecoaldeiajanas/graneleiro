<?php

	require_once("../storescripts/connectMysql.php");

	require_once("../includes/functions.php");
	
	@$targetID=$_GET['id'];
	
	$i=0;
	

	//$q = $conDB->sql_query("SELECT products.id, products.product_name, people.name, products.price, encomenda_has_products.quant, products.price*encomenda_has_products.quant, encomenda_has_products.aferirPeso, products.peso, products.unit FROM encomenda, encomenda_has_products, products, people WHERE encomenda.id_encomenda='$targetID' AND encomenda_has_products.id_produto=products.id AND encomenda_has_products.id_encomenda='$targetID' AND products.id_produtor=people.id_people ");
	
		$q = $conDB->sql_query("SELECT products.id, products.product_name, people.name, products.price, encomenda_has_products.quant, products.price*encomenda_has_products.quant, products.peso, products.unit FROM encomenda, encomenda_has_products, products, people WHERE encomenda.id_encomenda='$targetID' AND encomenda_has_products.id_produto=products.id AND encomenda_has_products.id_encomenda='$targetID' AND products.id_produtor=people.id_people ");

	while($r = $conDB->sql_fetchrow($q)) {
		 $aferirPeso = $r['aferirPeso'];
		 $peso = $r['peso'];
		 $unit = $r['unit'];
			 if($peso==1 && $aferirPeso==0){
					$quantidade=0;
					}else{
					$quantidade=1;
					$unid=" Kg";
					}
			 if($unit==1||($peso==1 && $aferirPeso==0)){
					$unid=" Unid.";
					}else{
					$unid=" Kg";
					}

		foreach ($r as $key => $value) {
			$i++;
			if($i==4){ $value=$value." Euros";}
			if($i==5){ $value=$value.$unid;}
			if($i==6){ $value=number_format(($value*$quantidade),2);}
			if(!@$_GET['normalJSON']) $r[$key] = $value;
			}
			@$return[] = $r;
			$i=0;
	
	}
	if(@$_GET['normalJSON'])
		echo json_encode(@$return);

	else
		echo utf8_decode(json_encode_dataTable(@$return));

	

?>
