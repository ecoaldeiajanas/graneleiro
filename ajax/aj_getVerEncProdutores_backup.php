<?php

	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");
	
	$obs='';
	$i=0;
	@$targetID=$_GET['id'];
	@$encID=$_GET['encID'];

	$q = $conDB->sql_query("SELECT products.id, products.product_name, products.price, SUM(encomendaprodutor_has_products.quant ), SUM(products.price*encomendaprodutor_has_products.quant) FROM encomendaprodutor_has_products, people, products WHERE encomendaprodutor_has_products.id_produtor=people.id_people
AND encomendaprodutor_has_products.id_produto=products.id  AND people.id_people='$targetID' AND encomendaprodutor_has_products.id_encomendaProdutor='$encID'  GROUP BY products.id ");

	while($r = $conDB->sql_fetchrow($q)) {
		
		foreach ($r as $key => $value) {
			$i++;
			if($i==1){
				$sql=mysql_query("SELECT * FROM products WHERE id='$value'");
					while($row=mysql_fetch_array($sql)){
						$peso=$row['peso'];
						$unit=$row['unit'];		
						}
							if($unit==1||$peso==1){
								$unid=" Unid.";
							}else{
								$unid=" Kg";
							}
							/*if($peso==1){
								$valueT=0;
								$obs="Necessario aferir o peso e acertar o valor";
							}else{
								$valueT=1;
								$obs="";
							}*/
			}
			if($i==5){ $value=number_format($value,2)." Euros";}
			if($i==4){ $value=$value.$unid;}
			if($i==3){ $value=$value." Euros";}
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
