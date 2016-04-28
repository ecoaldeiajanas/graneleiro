<?php
	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");


	// gather ----------
	@$quant = $_POST['quant'];
	@$id_encomenda = $_POST['id_encomenda'];
	@$isEdit = $_POST['isEdit'];
	
	// init accessories ----------
	$id = -1;
	
	// data treatment ----------
	if(!$quant || $quant==""){
		return false;
		die();
	}
	
	if($isEdit!="false"){
		$id=$isEdit;
		$isEdit=true;
		$aferirPeso=1;
	}else{
		$isEdit=false;
	}

	// insert or edit product --------------
	
		// edit product --------------
			//quantidade inicial da encomenda
			$sql=mysql_query("SELECT quant FROM encomenda_has_products WHERE id_encomenda=$id_encomenda AND id_produto=$id");
				while($row=mysql_fetch_array($sql)){
					$quantidadeInicial = $row['quant'];
				}
			//actualizar quantidade na encomenda	
			$q = $conDB->sql_query("UPDATE encomenda_has_products 
				SET quant='$quant', aferirPeso='$aferirPeso'
				WHERE  id_produto=$id AND id_encomenda=$id_encomenda", @BEGIN_TRANSACTION);

			$sql=mysql_query("SELECT * FROM encomenda, encomenda_has_products, products WHERE encomenda.id_encomenda='$id_encomenda' AND encomenda_has_products.id_produto=products.id AND encomenda_has_products.id_encomenda='$id_encomenda'");

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
			$sql=mysql_query("SELECT quantidade FROM products WHERE id=$id LIMIT 1");
				while($row=mysql_fetch_array($sql)){
					$quantidade = $row['quantidade'];
				}
				if($quantidadeInicial>$quant){
					$quantidadeFinal=$quantidadeInicial-$quant;
					$quantidade=$quantidade+$quantidadeFinal;
				}else{
					$quantidadeFinal=$quant-$quantidadeInicial;
					$quantidade=$quantidade-$quantidadeFinal;
				}
			$q = $conDB->sql_query("UPDATE products SET quantidade='$quantidade' WHERE id='$id'", @BEGIN_TRANSACTION);
			/*$q = $conDB->sql_query("UPDATE encomendatotal 
				SET total='$cartTotal' 
				WHERE id_encomenda=$id_encomenda", BEGIN_TRANSACTION);*/
		
	
	$q = $conDB->sql_query("",@END_TRANSACTION);
	


	if($q)
		echo json_encode($_POST);

?>
