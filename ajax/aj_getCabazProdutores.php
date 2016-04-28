<?php

	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");
	
	$i=0;


	$q = $conDB->sql_query("SELECT DISTINCT id_produtor, name, permissao, semana  FROM encomenda_has_products, people, encomenda  WHERE encomenda_has_products.id_produtor=people.id_people AND encomenda.id_encomenda=encomenda_has_products.id_encomenda AND encomenda.semana=0");

	while($r = $conDB->sql_fetchrow($q)) {
		foreach ($r as $key => $value) {
			
			$i++;
			if($i==1){
				$sql=mysql_query("SELECT * FROM encomenda, encomenda_has_products, people, products 
								WHERE encomenda_has_products.id_produtor=people.id_people
								AND encomenda.id_encomenda=encomenda_has_products.id_encomenda
								AND encomenda.semana=0 
								AND encomenda_has_products.id_produto=products.id  
								AND people.id_people='$value' ");
								$encomendaCount=mysql_num_rows($sql);
								if($encomendaCount>0){
									while($row=mysql_fetch_array($sql)){
										$id_produtor=$row["id_produtor"];
										$quant=$row["quant"];
										$name=$row['name'];
										$product_name=$row['product_name'];
										$price=$row['price'];
										$priceTotal=$price*$quant;
										@$cartTotal=$priceTotal+$cartTotal;
									}
								}
			}
			if($i==3){ $value=number_format($cartTotal,2)." - Euros";$cartTotal=0;}
			
			if(!@$_GET['normalJSON']) $r[$key] = $value;
		}
		$return[] = $r;
		$i=0;
	}
//echo "teste".$return[0]; exit();
	if(@$_GET['normalJSON'])
		echo json_encode($return);
	else
		echo utf8_decode(json_encode_dataTable($return));

	

?>
