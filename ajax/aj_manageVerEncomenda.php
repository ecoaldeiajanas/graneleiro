<?php
	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");


	// gather ----------
	@$quant = $_POST['quant'];
	@$id_encomenda = $_POST['id_encomenda'];
	@$total = $_POST['total'];
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
	}else{
		$isEdit=false;
	}

		//quantidade disponivel do produto
			$sql=mysql_query("SELECT quantidade FROM products WHERE id=$id LIMIT 1");
				while($row=mysql_fetch_array($sql)){
					$quantDisponivel = $row['quantidade'];
				}
	
		//quantidade inicial da encomenda
			$sql=mysql_query("SELECT quant FROM encomenda_has_products WHERE id_encomenda=$id_encomenda AND id_produto=$id");
				while($row=mysql_fetch_array($sql)){
					$quantidadeInicial = $row['quant'];
				}
		
		//Actualizar quantidade em encomendas_has_products
			if($quant<=$quantDisponivel+$quantidadeInicial){
				$quantF=$quant;
				$q = $conDB->sql_query("UPDATE products 
				SET obs=''
				WHERE  id=$id", @BEGIN_TRANSACTION);
			}else{
				$quantF=$quantidadeInicial+$quantDisponivel;
				// Msg-Qt de Prod. não disponivel
				$q = $conDB->sql_query("UPDATE products 
				SET obs='Quantidade disponível.'
				WHERE  id=$id", @BEGIN_TRANSACTION);
			}
			$q = $conDB->sql_query("UPDATE encomenda_has_products 
				SET quant='$quantF'
				WHERE  id_produto=$id AND id_encomenda=$id_encomenda", @BEGIN_TRANSACTION);
				
		// Actualizar Total em encomenda
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
			
				
			// Actualizar Quantidade na Encomenda
			
			/*$q = $conDB->sql_query("UPDATE encomenda_has_products 
				SET quant='$quant'
				WHERE  id_produto=$id AND id_encomenda=$id_encomenda", BEGIN_TRANSACTION);*/
				
			//Actualizar a Quantidade em Stock
			/*$sql=mysql_query("SELECT quantidade FROM products WHERE id=$id LIMIT 1");
				while($row=mysql_fetch_array($sql)){
					$quantidade = $row['quantidade'];
				}*/

				if($quantidadeInicial>$quant){
					$quantidadeFinal=$quantidadeInicial-$quantF;
					$quantDisponivel=$quantDisponivel+$quantidadeFinal;
				}else{
					$quantidadeFinal=$quantF-$quantidadeInicial;
					$quantDisponivel=$quantDisponivel-$quantidadeFinal;
				}

			$q = $conDB->sql_query("UPDATE products SET quantidade='$quantDisponivel' WHERE id='$id'", @BEGIN_TRANSACTION);
					
			$q = $conDB->sql_query("",@END_TRANSACTION);
	


	if($q)
		echo json_encode($_POST);

?>
