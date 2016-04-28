<?php
	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");

	if(!@$_POST["id_encomenda"]) die();

	@$id_encomenda = $_POST["id_encomenda"];
	@$id_produto = $_POST["id_produto"];
	@$quant = $_POST["quant"];
	@$total = $_POST["total"];
	
	
		//preço do produto
			$sql=mysql_query("SELECT price FROM products WHERE id='$id_produto'");
				while($row=mysql_fetch_array($sql)){
					$price = $row['price'];
				}
		//preço a retirar ao total 
				$retirarValor=$price*$quant;
				$novoTotal=$total-$retirarValor;
		//Actualizar Total
				$q = $conDB->sql_query("UPDATE encomenda 
				SET total='$novoTotal'
				WHERE  id_encomenda=$id_encomenda", @BEGIN_TRANSACTION);
		
		//remover produto da encomenda
				$q = $conDB->sql_query("DELETE FROM encomenda_has_products WHERE id_encomenda = $id_encomenda AND id_produto='$id_produto'", @BEGIN_TRANSACTION);
		//actualizar Quantidade em Stock
				$sql=mysql_query("SELECT quantidade FROM products WHERE id='$id_produto'");
				while($row=mysql_fetch_array($sql)){
					$quantidade = $row['quantidade'];
				}
				//Quantidade adicionar ao Stock
				$quantidade=$quantidade+$quant;
				
				$q = $conDB->sql_query("UPDATE products 
				SET quantidade='$quantidade'
				WHERE  id=$id_produto", @BEGIN_TRANSACTION);
				
				
	$q = $conDB->sql_query("",@END_TRANSACTION);

	if($q)
		echo json_encode($_POST);

?>
