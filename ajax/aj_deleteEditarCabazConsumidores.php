<?php
	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");
	
	//id_encomenda e id_produto
	//if(!$_POST["id_produto"]) die();

	@$id_produto = $_POST["id_produto"];
	@$id_encomenda = $_POST["id_encomenda"];

	$q = $conDB->sql_query("DELETE FROM encomenda_has_products WHERE id_encomenda = $id_encomenda AND id_produto=$id_produto",@BEGIN_TRANSACTION);
	
	
	$sql=mysql_query("SELECT * FROM encomenda, encomenda_has_products, products WHERE encomenda.id_encomenda='$id_encomenda' AND encomenda_has_products.id_produto=products.id AND encomenda_has_products.id_encomenda='$id_encomenda'");
	
	$existProduct=mysql_num_rows($sql);
	$cartTotal=0;
				while($row=mysql_fetch_array($sql)){
					//$date=$row['date'];
					$price=$row['price'];
					$quant=$row['quant'];
					$priceTotal=$price*$quant;
					@$cartTotal=$priceTotal+$cartTotal;
				}
			
			$q = $conDB->sql_query("UPDATE encomenda 
				SET total='$cartTotal' 
				WHERE id_encomenda=$id_encomenda", @BEGIN_TRANSACTION);
			
			//Actualizar a Quantidade em Stock
			$sql=mysql_query("SELECT quantidade FROM products WHERE id=$id_produto LIMIT 1");
				while($row=mysql_fetch_array($sql)){
					@$quantidade = $row['quantidade'];
				}
				@$quantidade=$quantidade+@$quant;
				
			$q = $conDB->sql_query("UPDATE products SET quantidade='$quantidade' WHERE id='$id_produto'", @BEGIN_TRANSACTION);


	
	if($existProduct==0){
	//remover encomenda caso seja o ultimo produto a ser removido
	$q = $conDB->sql_query("DELETE FROM encomenda WHERE id_encomenda='$id_encomenda'", @BEGIN_TRANSACTION);

	}	
	

	$q = $conDB->sql_query("",@END_TRANSACTION);

	if($q)
		echo json_encode($_POST);

?>
