<?php

	require_once("../storescripts/connectMysql.php");

	require_once("../includes/functions.php");
	
	$i=0;
	
	@$targetID=$_GET['id'];
	
	$q = $conDB->sql_query("SELECT id, product_name, products.imagem, price, category, quantidade, unit, peso, products.id_category, products.details, stock FROM products, category WHERE category.id_category=products.id_category AND products.id_produtor=$targetID");

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
			if($i==6){ $value=$value.$unid;}			
			if($i==7 || $i==8){ if($value==1){$value="Sim";}else{ $value="-";}}
			if($i==11){ if($value==1){$value="Sim";}else{$value="Não";}}
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
