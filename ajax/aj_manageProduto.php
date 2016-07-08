<?php
	require_once("../storescripts/connectMysql.php");
	require_once("../includes/functions.php");


	// gather ----------
	@$product_name = $_POST['product_name'];
	@$price = $_POST['price'];
	@$id_produtor = $_POST['id_produtor'];
	@$id_category = $_POST['id_category'];
	@$quantidade = $_POST['quantidade'];
	@$details = $_POST['details'];
	$details = trim($details);
	$details = str_replace("\"", "", $details);
	$details = str_replace("\r", "<br />", $details);
	$details = str_replace("\n", "", $details);
	
	@$imagem = $_POST['fileName'];
	@$isEdit = $_POST['isEdit'];

	// init accessories ----------
	$id = -1;
	$stock = 0;
	$unit = 0;
	$peso = 0;
	$cultura = "";

	// print_r($_POST);

	// data treatment ----------
	if(!$product_name || $product_name==""){
		return false;
		die();
	}

	if($isEdit!="false"){
		$id=$isEdit;
		$isEdit=true;
	}else{
		$isEdit=false;
	}

	// insert or edit product --------------
	if($isEdit){
		// edit product --------------
		if($imagem && $imagem!="")
			$q = $conDB->sql_query("UPDATE products 
				SET product_name='$product_name',imagem='$imagem', price='$price', id_produtor='$id_produtor', id_category='$id_category', quantidade='$quantidade', details='$details' 
				WHERE  id=$id", @BEGIN_TRANSACTION);
		else
			$q = $conDB->sql_query("UPDATE products 
				SET product_name='$product_name', price='$price', id_produtor='$id_produtor', id_category='$id_category', quantidade='$quantidade', details='$details'
				WHERE  id=$id", @BEGIN_TRANSACTION);
	}else{
		 //add product ------------------
		if($imagem && $imagem!="")
			$q = $conDB->sql_query("INSERT INTO products (product_name,  price, id_produtor, id_category, quantidade, details, imagem) VALUES ('$product_name','$price','$id_produtor','$id_category','$quantidade','$details','$imagem')", @BEGIN_TRANSACTION);
		else
			$q = $conDB->sql_query("INSERT INTO products (product_name, price, id_produtor, id_category, quantidade, details) VALUES ('$product_name','$price','$id_produtor','$id_category','$quantidade','$details')", @BEGIN_TRANSACTION);
		$id= $conDB->sql_nextid();
	}

	
	
	if(@$_POST["stock"]){
		$stock = 1;
	}else{
		$stock = 0;
		}	
		$q = $conDB->sql_query("UPDATE products SET stock='$stock' WHERE id='$id'");
	
	if(@$_POST["unit"]){
		$unit = 1;
	}else{
		$unit = 0;
		}
		$q = $conDB->sql_query("UPDATE products SET unit='$unit' WHERE id='$id'");
		
	if(@$_POST["peso"]){
		$peso = 1;
	}else{
		$peso = 0;
		}
		$q = $conDB->sql_query("UPDATE products SET peso='$peso' WHERE id='$id'");
		
	
	if(@$_POST["b"]){
		$q = $conDB->sql_query("UPDATE products SET cultura='Biológico' WHERE id='$id'");
		$cultura ="Biológico";
	}
	elseif(@$_POST["c"]){
		$q = $conDB->sql_query("UPDATE products SET cultura='Conversão-Bio' WHERE id='$id'");
		$cultura ="Conversão-Bio";
	}
	elseif(@$_POST["p"]){
		$q = $conDB->sql_query("UPDATE products SET cultura='Orgânico' WHERE id='$id'");
		$cultura ="Orgânico";
	}elseif(@$_POST["prot"]){
		$q = $conDB->sql_query("UPDATE products SET cultura='Integrada​' WHERE id='$id'");
		$cultura ="Integrada​";
	}elseif(@$_POST["protrad"]){
		$q = $conDB->sql_query("UPDATE products SET cultura='Tradicional' WHERE id='$id'");
		$cultura ="Tradicional";
	}elseif(@$_POST["prodconv"]){
		$q = $conDB->sql_query("UPDATE products SET cultura='Convencional' WHERE id='$id'");
		$cultura ="Convencional";
	}else{
		$q = $conDB->sql_query("UPDATE products SET cultura='' WHERE id='$id'");
		$cultura ="";
	}
	
	if(!$q){
		$r = $conDB->sql_error($q);
		echo $r["code"];
	}
	
	$q = $conDB->sql_query("",@END_TRANSACTION);
	
	if(!$q) die();

	$_POST['id'] = $id;
	$_POST['stock'] = $stock;
	$_POST['unit'] = $unit;
	$_POST['peso'] = $peso;
	$_POST['cultura'] = $cultura;

	if($q)
		echo json_encode($_POST);

?>
