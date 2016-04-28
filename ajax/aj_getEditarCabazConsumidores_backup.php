<?php

	require_once("../storescripts/connectMysql.php");

	require_once("../includes/functions.php");
	
	@$targetID=$_GET['id'];
	
	
	$i=0;
	

	$q = $conDB->sql_query("SELECT products.id, products.product_name, people.name, products.price, encomenda_has_products.quant, products.price*encomenda_has_products.quant, semana FROM encomenda, encomenda_has_products, products, people WHERE encomenda.id_encomenda='$targetID' AND encomenda_has_products.id_produto=products.id
AND encomenda.id_encomenda=encomenda_has_products.id_encomenda
AND encomenda.semana=1 
AND encomenda_has_products.id_encomenda='$targetID' 
AND products.id_produtor=people.id_people ");

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
			if($i==4){ $value=$value." Euros";}
			if($i==6){ $value=number_format($value,2);}
			if($i==5){ $value=$value.$unid;}
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
