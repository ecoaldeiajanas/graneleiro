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
    @$details2 = trim($details2);
    $details2 = str_replace("\r", "<br />", $details);
    $details2 = str_replace("\n", "", $details2);
    @$imagem = $_POST['fileName'];
    @$isEdit = $_POST['isEdit'];

    // init accessories ----------
    $id = -1;
    $unit = 0;
    $peso = 0;

    // data treatment ----------
if (!$product_name || $product_name=="") {
    return false;
    die();
}

if ($isEdit!="false") {
    $id=$isEdit;
    $isEdit=true;
} else {
    $isEdit=false;
}

    // insert or edit product --------------
if ($isEdit) {
    // edit product --------------
    if ($imagem && $imagem!="") {
        $q = $conDB->sql_query("UPDATE products 
				SET product_name='$product_name',imagem='$imagem', price='$price', id_produtor='$id_produtor', id_category='$id_category', quantidade='$quantidade', details='$details2' 
				WHERE  id=$id", @BEGIN_TRANSACTION);
    } else {
        $q = $conDB->sql_query("UPDATE products 
				SET product_name='$product_name', price='$price', id_produtor='$id_produtor', id_category='$id_category', quantidade='$quantidade', details='$details2'
				WHERE  id=$id", @BEGIN_TRANSACTION);
    }
} else {
 //add product ------------------
    if ($imagem && $imagem!="") {
        $q = $conDB->sql_query("INSERT INTO products (product_name,  price, id_produtor, id_category, quantidade, details, imagem) VALUES ('$product_name','$price','$id_produtor','$id_category','$quantidade','$details2','$imagem')", @BEGIN_TRANSACTION);
    } else {
        $q = $conDB->sql_query("INSERT INTO products (product_name, price, id_produtor, id_category, quantidade, details) VALUES ('$product_name','$price','$id_produtor','$id_category','$quantidade','$details2')", @BEGIN_TRANSACTION);
    }
    $id= $conDB->sql_nextid();
}

if (@$_POST["unit"]) {
    $unit = 1;
} else {
    $unit = 0;
}
        $q = $conDB->sql_query("UPDATE products SET unit='$unit' WHERE id='$id'");
        
if (@$_POST["peso"]) {
    $peso = 1;
} else {
    $peso = 0;
}
        $q = $conDB->sql_query("UPDATE products SET peso='$peso' WHERE id='$id'");
    
if (!$q) {
    $r = $conDB->sql_error($q);
    echo $r["code"];
}
    
    @$q = $conDB->sql_query("", @END_TRANSACTION);
    
if (!$q) {
    die();
}

    @$_POST['id'] = $id;
    $_POST['unit'] = $unit;
    $_POST['peso'] = $peso;

if ($q) {
    echo json_encode($_POST);
}
