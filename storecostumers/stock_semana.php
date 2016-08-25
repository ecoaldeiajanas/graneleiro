<?php
ob_start();
session_start();
require_once("../includes/core/header.php");
require_once("../storescripts/connectMysql.php");

 $logged = ($_SESSION["id"] && $_SESSION["id"]!=null);
if ($logged) {
    $id = $_SESSION["id"];
    $name = $_SESSION["name"];
    $firstName = explode(" ", $name);
    $firstName = $firstName[0];
    $admin = $_SESSION["manager"];
    $produtor = $_SESSION["produtor"];
} else {
    header("location:../login.php");
    exit();
}
?>
<?php

//Verificar se existe encomenda da semana
     $sql=mysql_query("SELECT id_people, semana FROM encomenda WHERE id_people=$id AND semana=0  LIMIT 1");
        $existEnc=mysql_num_rows($sql);
?>
<?php
// Verificar se site está bloqueado
    $sql=mysql_query("SELECT * FROM block  LIMIT 1");

while ($row=mysql_fetch_array($sql)) {
    $block=$row['block'];
}
if ($block==0) {
    $erro="Site bloqueado! <br>Só são permitidas encomendas no proximo Domingo.";
} else {
?>

<?php
//Listar Categorias
$category_list='';
$sql=mysql_query("SELECT * FROM category ORDER BY category ASC");
while ($row=mysql_fetch_array($sql)) {
    $id_category=$row['id_category'];
    $category=$row['category'];
    @$idCat=$_GET['idCat'];


    //$category_list.='<ul>';
    if ($idCat==$id_category) {
        $category_list.='<li style="padding: 15px 0;padding-left: 10px; "><b>'.$category.'</b></li>';
    } else {
        $category_list.='<li><a href="stock_semana.php?idCat='.$id_category.'">'.$category.'</a></li>';
    }

    //$category_list.='</ul>';
}
?>

<?php
////////////////////////////////////////////////////////////////////////////////////////////////////
//Run a Select Query to View product Items
////////////////////////////////////////////////////////////////////////////////////////////////////
$dynamicListH='';
$sinal='';
$add='0';
$id_category='1';
$tipoProduto="";

if (isset($_GET['idCat'])) {
        $id_category=$_GET['idCat'];
}

// Adicionar Produto a Encomenda
if (isset($_GET['add'])) {
    $add=$_GET['add'];
    $id_encomenda=$_GET['idEnc'];
    $total=$_GET['t'];
    $sql=mysql_query("SELECT * FROM products,people WHERE products.quantidade>0 AND products.stock=1 AND people.ferias=0 AND products.id_produtor=people.id_people ORDER BY product_name ASC ");
} else {
    $sql=mysql_query("SELECT * FROM products, people WHERE products.id_category=$id_category AND products.stock=1 AND people.ferias=0 AND products.id_produtor=people.id_people ORDER BY product_name ASC ");
}
$productCount=mysql_num_rows($sql);
if ($productCount>0) {
    while ($row=mysql_fetch_array($sql)) {
        $id=$row['id'];
        $quantidade=$row['quantidade'];
        $stock=$row['stock'];
        $product_name=$row['product_name'];
        $price=$row['price'];
        $unit=$row['unit'];
        $produtorName=$row['name'];
        $cultura=$row['cultura'];
        $details=$row['details'];
        $imagem=$row['imagem'];
        if ($cultura=='' || $cultura=='. ') {
            $cultura=' <h5 style="color:white;">.</h5> ';
        }
        $date_added=strftime("%b %d %y", strtotime($row['date_added']));
        if ($unit=='1') {
            $sinal=" €/unid.";
        } else {
            $sinal=" €/Kg";
        }
        if ($quantidade>0 && $stock==1) {
            $input="comprar";
            $value="Encomendar";
            $pagina="product.php";
            $encomendar='<input name="'.$input.'" id="'.$input.'" type="button" value="'.$value.'" />';
        } else {
            $input="esgotado";
            $value="Produto esgotado ";
            $pagina="#";
            $encomendar='<img src="../p_images/esgotado.png" width="130" height="40" />';
        }

        @$dynamicListT.='<table width="100%" border="0" cellspacing="0" cellpadding="0" id="pid'.$id.'">
  <tr>
    <th colspan="3" scope="col" align="left"><p style="max-height:10px; min-height:10px;"><b>'.$product_name.'</b></p><br/></th>
  </tr>
  <tr>
    <td width="16%" align="left" style="vertical-align:top"><img class="top2" src="../p_images/'.$imagem.'" width="100" height="100" /></a></td>
    <td width="74%" align="left"><b style="color:orange">'.$price.''.$sinal.'</b><br /><br/>Produtor - '.$produtorName.'<br/><br/>
	<h5>'.$cultura.'</h5></td>
    <td width="10%" align="right" style="vertical-align: bottom"><a href="'.@$pagina.'?pid='.@$id.'&add='.@$add.'&idEnc='.@$id_encomenda.'&t='.@$total.'&cat='.@$id_category.'">'.@$encomendar.'</a></td>
  </tr>
</table><hr>';
    }@$i++;
} else {
    @$dynamicListT="<br/>De momento não temos Produtos disponíveis.";
}
}//fechar block
//mysqli_close($conDB);
?>

    <script>
   $(function() {
    $( "input[type=button]" )
      .button()
      $("comprar").click(function( event ) {
        event.preventDefault();
      });
  });
$(function() {
    $( "input[type=button]" )
      .button()
      $("esgotado").click(function( event ) {
        event.preventDefault();
      });
  });
  </script>
<div id="motherContainer">
        <div id="header">
            <div id="smalllogo"></div>
            <!--<div class="heading"><h1><a href="index.php">QUEM SOMOS</a></h1></div>
            <div class="heading"><?php if ($existEnc>0) {?>
                                    <h1><a href="#">STOCK DA SEMANA</a></h1></div>

                                    <?php } else {?>
                                    <?php if ($existEnc>0) {?>
                                    <h1><a href="#">STOCK DA SEMANA</a></h1></div>

                                    <?php } else {?>
                                    <h1><a href="stock_semana.php?idCat=1">STOCK DA SEMANA</a></h1></div>
                                    <?php }?>
                                    <?php }?>
       <div class="heading"><h1><a href="servicos.php">SERVIÇOS</a></h1></div>-->
    <div class="heading">
        <ul>
          <li><h1><a href="index.php">QUEM SOMOS</a></h1></li>
                <?php if ($existEnc>0) {?>
                                        <li><h1><a href="#">STOCK DA SEMANA</a></h1></li>

                                    <?php } else {?>
                                    <li><h1><a href="stock_semana.php">STOCK DA SEMANA</a></h1></li>
                                    <?php }?>
               <li><h1><a href="servicos.php">SERVIÇOS</a></h1></li>
               <li><h1><a href="info.php">Info</a></h1></li>
        </ul>
        </div>
            <div class="lastHeading">
                <div class="userOptions">
                     <div id="userOptionsInner">
                            <?php if ($existEnc==1) {
                                echo '<h1 ><a href="ver_encomenda.php">VER ENCOMENDA</a></h1>';
} else {
    echo '<h1 ><a href="cart.php">O MEU CABAZ</a></h1>';
}?>
                            <?php if ($admin) {
                                 echo '<h1 ><a href="../storeadmin/index.php">ADMIN</a></h1>';
}?>
                            <?php if ($produtor) {
                                 echo '<h1 ><a href="../storeprodutor/index.php">PRODUTOR</a></h1>';
}?>
                                      <h1 ><a href="logout.php">SAIR</a></h1>
                    </div>
                </div>
            </div>
        </div>
        <div id="headerDrawer">
        </div>
    <div id="pageContainer">
    <h2><p style="margin-left:13px;">Stock da Semana</p></h2><hr>
   <p> <?php echo @$erro; ?></p>
    <p >
        <?php if (!isset($_GET['add'])) {?>
    </p>
    <div   style="width:230px;float:left;">
    <div align="left" style="margin-left:5px ">

    <ul id="verticalmenu" class="glossymenu">
<li><?php  echo $category_list;?></li>
</ul>
      </div>
    </div>
         <div id="productsww" style="width:720px;float:left;margin-left:30px ;">
        <?php echo $dynamicListT; ?>

    <?php } else {?>
        <div id="productsww" style="width:900px;float:left;margin-left:50px; margin-right:50px;">

        <?php echo $dynamicListT;
}?>
        
      </div>
      </div>
      <a href="#" class="scrollup-link"><div class="scrollup-inside hidden"><span class="glyphicon glyphicon-chevron-up"></span></div></a>

        <?php require_once("../includes/core/footer.php"); ?>
    </div>

</body></html>
