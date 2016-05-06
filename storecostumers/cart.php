<?php
ob_start();
session_start();
require_once("../includes/core/header.php");
require_once("../storescripts/connectMysql.php");
require_once("../includes/phpmailer/class.phpmailer.php");

    $logged = ($_SESSION["id"] && $_SESSION["id"]!=null);
if ($logged) {
    $id = $_SESSION["id"];
    $name = $_SESSION["name"];
    $firstName = explode(" ", $name);
    $firstName = $firstName[0];
    $admin = $_SESSION["manager"];
    $produtor = $_SESSION["produtor"];
    $email = $_SESSION["email"];
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

////////////////////////////////////////////////////////////////////////////////////
// Section 1 (if user attempts to add something to the cart from procuct page)
//////////////////////////////////////////////////////////////////////////////////// 
if (isset($_POST['pid'])) {
    $pid=$_POST['pid'];
    $id_category=$_POST['cat'];
    $quantity=$_POST['quantity'];
    $wasFound =false;
    $i=0;


        
    //if the cart session variable is not set or cart is empty
    if (!isset($_SESSION["cart_array"]) || count($_SESSION["cart_array"])< 1) {
        //Run if the Cart is empty or not set
        $_SESSION["cart_array"]=array(0=>array("item_id"=> $pid, "quantity"=>$quantity));
    } else {
        //Run if the Cart has at at least one item in It
        foreach ($_SESSION["cart_array"] as $each_item) {
            $i++;
            while (list($key, $value) = each($each_item)) {
                if ($key == "item_id" && $value == $pid) {
                    array_splice($_SESSION["cart_array"], $i-1, 1, array(array("item_id" =>$pid, "quantity" => $each_item['quantity'] +$quantity)));
                    $wasFound=true;
                }
            }
        }
        if ($wasFound == false) {
            array_push($_SESSION["cart_array"], array("item_id"=> $pid, "quantity"=>$quantity));
        }
    }
    // Redirect with category id and product id for returning purpose
    header("location:cart.php?idCat=" . $id_category ."&pid=" . $pid);
    exit();
}
?>
<?php
////////////////////////////////////////////////////////////////////////////////////
// Section 2 (if user choose to empty their shopping cart)
////////////////////////////////////////////////////////////////////////////////////
if (isset($_GET['cmd']) && $_GET['cmd']=="emptycart") {
    unset($_SESSION["cart_array"]);
}
?>
<?php
////////////////////////////////////////////////////////////////////////////////////
// Section 3 (if user choose to Order shopping cart)
////////////////////////////////////////////////////////////////////////////////////
$id_people=$_SESSION["id"];
if (isset($_GET['cmd']) && $_GET['cmd']=="finalOrder") {
    $cartOutput="";
    $cartTotal="";

    if (!isset($_SESSION["cart_array"]) || count($_SESSION["cart_array"])< 1) {
        $cartOutput="<label for='title'><h2>O Cabaz Semanal está vazio.</h2><hr/><br />
	  </label>";
    } else {
        //Se existir produto no cart
        foreach ($_SESSION["cart_array"] as $each_item) {
            $item_id = $each_item['item_id'];
            $sql=mysql_query("SELECT * FROM products WHERE id='$item_id' LIMIT 1");
            while ($row=mysql_fetch_array($sql)) {
                $pid=$row['id'];
                $product_name=$row["product_name"];
                $price=$row["price"];
                $quantidade=$row["quantidade"];
                $peso=$row['peso'];
            
                $quantity=$each_item['quantity'];
            
                if ($each_item['quantity']<$quantidade) {
                    if ($peso=='1') {
                        $priceTotal=$price*0;
                        $cartTotal=$priceTotal+$cartTotal;
                        $quantity=$each_item['quantity'];
                    } else {
                        $priceTotal=$price*$each_item['quantity'];
                        $cartTotal=$priceTotal+$cartTotal;
                        $quantity=$each_item['quantity'];
                    }
                } else {
                    if ($peso=='1') {
                        $priceTotal=$price*0;
                        $cartTotal=$priceTotal+$cartTotal;
                        $quantity=$quantidade;
                    } else {
                        $priceTotal=$price*$quantidade;
                        $cartTotal=$priceTotal+$cartTotal;
                        $quantity=$quantidade;
                    }
                }
            }
        }
        ///////////////////////////////////////////////////////
        //Inserir dados na tabela encomenda
        $id_people=$_SESSION["id"];
        $q = $conDB->sql_query("INSERT INTO encomenda(id_people,total,date,semana)
		VALUES('$id_people','$cartTotal',now(),0)", @BEGIN_TRANSACTION) ;
        $id_encomenda=mysql_insert_id();

        foreach ($_SESSION["cart_array"] as $each_item) {
            $item_id = $each_item['item_id'];
            $sql=mysql_query("SELECT * FROM products WHERE id='$item_id' LIMIT 1");
            while ($row=mysql_fetch_array($sql)) {
                $pid=$row['id'];
                $product_name=$row["product_name"];
                $price=$row["price"];
                $quantidade=$row['quantidade'];
                $id_produtor=$row['id_produtor'];
        
                if ($each_item['quantity']<$quantidade) {
                    $quant=$each_item['quantity'];
                } else {
                    $quant=$quantidade;
                }
        
        //Inserir dados na tabela encomenda_has_products
                $q = $conDB->sql_query("INSERT INTO encomenda_has_products(id_encomenda,id_produto,id_produtor,quant)
		VALUES('$id_encomenda', '$pid', '$id_produtor','$quant')", @BEGIN_TRANSACTION) ;
        
                $quantidadeFinal=$quantidade-$quant;
        
        // Actualizar stock
                $q = $conDB->sql_query("UPDATE products SET   quantidade='$quantidadeFinal' WHERE id='$item_id'", @BEGIN_TRANSACTION) ;
            }
            $q = $conDB->sql_query("", @END_TRANSACTION);
        }

        unset($_SESSION["cart_array"]);
        //Buscar encomenda
        $targetID=$id_encomenda;
        $dynamicList='';
    
        $sql=mysql_query("SELECT * FROM encomenda, encomenda_has_products, products WHERE encomenda.id_encomenda='$targetID' AND encomenda_has_products.id_produto=products.id AND encomenda_has_products.id_encomenda='$targetID' ");

        while ($row=mysql_fetch_array($sql)) {
              $id_encomenda=$row["id_encomenda"];
              $product_name=$row['product_name'];
              $quantidade=$row['quant'];
              $total=$row['total'];
              $date=$row['date'];
              $price=$row['price'];
              $pid=$row['id'];
              $produtor=$row['id_produtor'];
              $details=$row['details'];
              $unit=$row['unit'];
              $peso=$row['peso'];
            if ($peso=='1') {
                $precoTotal=$price*0;
            } else {
                $precoTotal=$price*$quantidade;
            }
              //Lista Dinamica
              $dynamicList.='<tr>';
              $dynamicList.='<td class="cart">'.$product_name.'</th>';
            if ($peso=='1') {
                $dynamicList.='<td style="color:red" class="cart">Necessário aferir o peso e acertar o valor</th>';
            } else {
                $dynamicList.='<td class="cart"></th>';
            }
              $dynamicList.='<td class="cart" align="center"> '.$price.' €</td>';
            if ($unit=='1' || $peso=='1') {
                $dynamicList.='<td class="cart" align="center"> '.number_format($quantidade, 0).'-Unid.</th>';
            } else {
                $dynamicList.='<td class="cart" align="center"> '.$quantidade.'-Kg</th>';
            }
            
            $dynamicList.='<td class="cart" align="center"> '.number_format($precoTotal, 2).' €</td>';
        
            $dynamicList.='</tr>';
        }
        ///////////////////////////////////////////////////////////////////////////////
        //email para o Consumidor
    
        $ownerEmail='graneleiro@ecoaldeiajanas.org';
        //$array=(explode(" ",$name));
        //$firstName=$array[0];

        $totalTaxa=$total+$total*10/100;

        $to = $email;
    
        $subject = 'Encomenda da semana.';
    
        $headers = "From: Prossumidores  <" . strip_tags($ownerEmail) . ">\n";
        $headers .= "Reply-To: ". strip_tags($ownerEmail) . "\r\n";
        //$headers .= "CC: susan@example.com\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=utf-8\r\n";
    
        $message = '<html><body>';
        $message .='<p>Viva '.$firstName.'!</p>';
        $message .='<p>Caso pretenda alterar a encomenda aceda a "VER ENCOMENDA" na página dos Prossumidores.</p>';
        $message .= '<table width="80%" border="1" cellspacing="2" cellpadding="0">';
        $message .=' <tr>';
        $message .='<th width="18%" class="prod" align="left" bgcolor="#c5cda8" scope="col"><b>Produto</b></th>';
        $message .='<th width="45%" align="left" bgcolor="#c5cda8" class="prod" scope="col"><b>Observações</b></th>';
        $message .='<th width="13%" class="prod" bgcolor="#c5cda8" scope="col"><b>Preço Unit.</b></th>';
        $message .='<th width="13%" class="prod" align="center" bgcolor="#c5cda8" scope="col"><b>Quantidade</b></th>';
        $message .='<th width="11%" class="prod" bgcolor="#c5cda8" scope="col"><b>Total</b></th>';
        $message .='</tr>';
        $message .=  '<tr>'.$dynamicList.'</tr>';
        $message .= '</table>';
        $message .='<br/><p align="left"><b>Total da Encomenda: '.number_format($total, 2).' - euros </b></br><b>Total da Encomenda+Taxa: <font style="color:red">'.number_format($totalTaxa, 2).' - euros </b></font></br>* Ao preço total da encomenda está indexada uma taxa de 10% que ajudará na sustentabilidade do sistema.</p>';
        $message .= '</body></html>';
    
        mail($to, $subject, $message, $headers);
    
        header("location:final_order.php?id=$id_encomenda");
        exit();
    }
}
?>

<?php
////////////////////////////////////////////////////////////////////////////////////
// Section 5 (if user wants to remove an item from cart)
////////////////////////////////////////////////////////////////////////////////////
if (isset($_POST['index_to_remove']) && $_POST['index_to_remove']!="") {
    $key_to_remove= $_POST['index_to_remove'];
    echo $key_to_remove;
    //echo 'index-'.$key_to_remove.': Count-';
    if (count($_SESSION["cart_array"])<=1) {
        unset($_SESSION["cart_array"]);
    } else {
        unset($_SESSION["cart_array"]["$key_to_remove"]);
        sort($_SESSION["cart_array"]);
    }
    header("location:cart.php");
        exit();
}
 
?>
<?php
////////////////////////////////////////////////////////////////////////////////////
// Section 6 (render the cart)
////////////////////////////////////////////////////////////////////////////////////
$cartOutput="";
$cartTotal="";
$pp_checkout_btn='';
$i=0;
if (!isset($_SESSION["cart_array"]) || count($_SESSION["cart_array"])< 1) {
} else {
    $peso='';
    foreach ($_SESSION["cart_array"] as $each_item) {
        $item_id = $each_item['item_id'];
        $sql=mysql_query("SELECT * FROM products WHERE id='$item_id' LIMIT 1");
        while ($row=mysql_fetch_array($sql)) {
            $product_name=$row["product_name"];
            $price=$row["price"];
            $details=$row['details'];
            $quantidade=$row['quantidade'];
            $peso=$row['peso'];
            $unit=$row['unit'];
        }
    
        $quantidadePedida=$each_item['quantity'];

    //Dynamic table row assembly
        if ($quantidade>=$quantidadePedida) {
            if ($peso=='1') {
                $priceTotal=$price*0;
                $cartTotal=$priceTotal+$cartTotal;
            } else {
                $priceTotal=$price*$each_item['quantity'];
                $cartTotal=$priceTotal+$cartTotal;
            }
        
            $cartOutput.='<tr>';
            $cartOutput.='<td><b><a href="product.php?pid='.$item_id.'">'.$product_name.'</a><b></td>';
            if ($peso=='1') {
                    $cartOutput.='<td style="color:red" >Necessário aferir o peso e acertar o valor</td>';
            } else {
                $cartOutput.='<td></td>';
            }
            if ($unit=='1') {
                $cartOutput.='<td><h3 align="center">'.$price.' €/Unid.<h3></td>';
            } else {
                $cartOutput.='<td><h3 align="center">'.$price.' €/Kg<h3></td>';
            }
            if ($unit=='1' || $peso=='1') {
                $cartOutput.='<td><h3 align="center">'.$each_item['quantity'].' Unid.<h3></td>';
            } else {
                $cartOutput.='<td><h3 align="center">'.$each_item['quantity'].' kg<h3></td>';
            }
        
                $cartOutput.='<td><h3 align="center">'.number_format($priceTotal, 2).' Euros<h3></td>';
        
                $cartOutput.='<td><form action="cart.php" method="post"><input name="deleteBtn'.$item_id.'" type="submit" title="Remover Produto" value="X"/><input name="index_to_remove" type="hidden" value="'.$i.'"/></form></td>';
                $cartOutput.='</tr>';
                $i++;
        } else {
            $priceTotal=$price*$quantidade;
            $cartTotal=$priceTotal+$cartTotal;
        
        
            $cartOutput.='<tr>';
            $cartOutput.='<td><a href="product.php?id='.$item_id.'">'.$product_name.'</a><br/></td>';
            if ($peso=='1') {
                $cartOutput.='<td>Necessário aferir o peso.</td>';
            } else {
                $cartOutput.='<td></td>';
            }
            if ($unit=='1') {
                $cartOutput.='<td><h3 align="center">'.$price.' €/Unid.<h3></td>';
            } else {
                $cartOutput.='<td><h3 align="center">'.$price.' €/Kg<h3></td>';
            }
            if ($unit=='1' || $peso=='1') {
                $cartOutput.='<th><h3 align="center">Quantidade Disponível </br> '.$quantidade.' Unid.<h3></th>';
            } else {
                $cartOutput.='<th><h3 align="center">Quantidade Disponível </br> '.$quantidade.' Kg<h3></th>';
            }
    
                $cartOutput.='<td><h3 align="center">'.number_format($priceTotal, 2).' Euros<h3></td>';
        
                $cartOutput.='<td><form action="cart.php" method="post"><input id="deleteBtn" name="deleteBtn'.$item_id.'" type="submit" value="X"/><input name="index_to_remove" type="hidden" value="'.$i.'"/></form></td>';
                $cartOutput.='</tr>';
        }
    }
}

?>

<?php require_once("../includes/core/header.php"); ?> 

<div id="motherContainer">
        <div id="header">
            <div id="smalllogo"></div>
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
    <h2>Cabaz Semanal</h2><hr>
    <div align="left" style="margin-left:24px; margin-right:24px; ">
    
    
    <br />
    <?php
    if ($existEnc==1) {
        echo "<h2 align='center'>Não é permitido mais de uma encomenda por semana.</h2><br/>";
        echo "<h3 align='center'><b>Pode visualizar a encomenda realizada esta semana.</b></h3><br/></div>";
    } else {?>
    <div class="CSSTableGenerator" >
    <table >
      <tr>
         <td><b>Produto</b></td>
         <td><b>Observação</b></td>
         <td><b>Preço</b></td>
         <td width="10%"><b>Quantidade</b></td>
         <td width="11%" ><b>Total</b></td>
         <td width="4%" align="center"><b>&nbsp;</b></td>
      
        <?php echo $cartOutput;  ?>
      
    
    </table></div><br/><br/>
    <div align="right"><b><?php echo "Valor Total da Encomenda: ".number_format(round($cartTotal, 2), 2)." - Euros"; ?></b></div>
    <br />
    
    <script>
   $(function() {
    $( "input[type=button]" )
      .button()
      $("empty").click(function( event ) {
        event.preventDefault();
      });
      
      $( "input[type=button]" )
      .button()
      $("order").click(function( event ) {
        event.preventDefault();
      });
      
      $( "input[type=submit]" )
      .button()
      $("deleteBtn").click(function( event ) {
        event.preventDefault();
      });
     
  });
  </script>
 
    <div align="right">
    <a href="cart.php?cmd=emptycart"><input name="empty" id="empty" type="button" value="Esvaziar Cabaz" /></a>
    <!-- Redirect to last product visited on main store -->
    <input type="button" value="Continuar a Comprar" onclick="window.location='stock_semana.php?idCat=<?php echo $_GET['idCat']; ?>#pid<?php echo $_GET['pid']; ?>'">
    <a href="cart.php?cmd=finalOrder"><input id="order" name="order" type="button" value="Encomendar" /></a>
    </div>
    </div>
    <p><br/>
    </p>
<?php
    }
?>
      </div>
        <?php require_once("../includes/core/footer.php"); ?>
    </div>

</body></html>
