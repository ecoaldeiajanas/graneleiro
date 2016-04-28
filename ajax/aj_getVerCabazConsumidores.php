<?php

	require_once("../storescripts/connectMysql.php");

	require_once("../includes/functions.php");
	
	$i=0;
	
	$targetID=$_GET['id'];
	
	$q = $conDB->sql_query("SELECT products.id, products.product_name, people.name, products.price, encomenda_has_products.quant, products.price*encomenda_has_products.quant  FROM encomenda, encomenda_has_products, products, people  WHERE encomenda.id_encomenda='$targetID' AND encomenda_has_products.id_produto=products.id AND encomenda_has_products.id_produtor=people.id_people AND encomenda_has_products.id_encomenda='$targetID' ");

	while($r = $conDB->sql_fetchrow($q)) {
		
		foreach ($r as $key => $value) {
			$i++;
			if($i==1){
				$sql=mysql_query("SELECT * FROM products WHERE id='$value'");
					while($row=mysql_fetch_array($sql)){
						$unit=$row['unit'];	
						$peso=$row['peso'];		
						}
							if($unit==1 || $peso==1){
								$unid=" Unid.";
							}else{
								$unid=" Kg";
							}
			}
			if($i==4){ $value=$value." Euros";}
			if($i==5){ $value=$value.$unid;}
			if($i==6){ $value=number_format($value,2)." Euros";}

			if(!$_GET['normalJSON']) $r[$key] = utf8_decode($value);
			}
			$return[] = $r;
			$i=0;
		
}
	if($_GET['normalJSON'])
		echo json_encode($return);
	else
		echo utf8_decode(json_encode_dataTable($return));

	

?>