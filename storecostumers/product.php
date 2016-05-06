<?php
ob_start();
    session_start();
if (!isset($_SESSION["cliente"]) && !isset($_SESSION["manager"])  && !isset($_SESSION["produtor"])) {
    header("location:../login.php");
    exit();
}
        ?>
<?php
//Verificar se existe encomenda da semana
include"../storescripts/connectMysql.php";

$id_people=$_SESSION["id"];

     $sql=mysql_query("SELECT * FROM encomenda WHERE id_people=$id_people AND semana=0 LIMIT 1");
        $existEnc=mysql_num_rows($sql);
//Verificar ADMIN		
      $sql=mysql_query("SELECT id_people, flag FROM people WHERE id_people=$id_people LIMIT 1");
while ($row=mysql_fetch_array($sql)) {
    $flag=$row['flag'];
}
        
?>

<?php
 
    
//Checck to see the URL variable is set and that it exists in the database
$add=0;
$erro='';
if (isset($_GET['pid'])) {
    $add=$_GET['add'];
    $total=$_GET['t'];
    $id_encomenda=$_GET['idEnc'];
    $id_category=$_GET['cat'];
    $pid = preg_replace('#[^0-9]#', '', $_GET['pid']);
    // verificar se Produto existe na Encomenda
    $sql=mysql_query("SELECT * FROM encomenda_has_products WHERE id_produto=$pid AND id_encomenda='$id_encomenda' LIMIT 1");
    $existProd=mysql_num_rows($sql);
    if ($existProd>0) {
        $erro='Produto já existe na Encomenda Semanal!';
    }
    //Use this var to check to see if this ID exists, if yes then get the product detail, if no then exit this script and give message why
    $sql=mysql_query("SELECT * FROM products, people WHERE products.id='$pid' AND people.id_people=products.id_produtor LIMIT 1");
    $productCount=mysql_num_rows($sql);
    if ($productCount>0) {
        //Get all the product details
        while ($row=mysql_fetch_array($sql)) {
            $product_name=$row["product_name"];
            $price=$row["price"];
            $details=$row["details"];
            @$category=$row["category"];
            $produtor=$row["name"];
            $concelho=$row['concelho'];
            $id_produtor=$row['id_produtor'];
            $quantidade=$row["quantidade"];
            $unit=$row['unit'];
            $peso=$row['peso'];
            $imagem=$row['imagem'];
            $cultura=$row['cultura'];
            $date_added=strftime("%b %d %y", strtotime($row["date_added"]));
        }
    } else {
        echo "That item does not exist";
    }
} else {
    echo "Data to render this page is missing.";
    exit();
}

//mysqli_close($conDB);
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
                            <?php if ($flag=='ac'|| $flag=='acp') {
                                 echo '<h1 ><a href="../storeadmin/index.php">ADMIN</a></h1>';
}?>
                            <?php if ($flag=='cp') {
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
     <script>
         $(function() {
            $( "input[type=submit], button" )
            .button()
            $( "form1" ).click(function( event ) {
            event.preventDefault();
          });
        });
    </script>
    <h2><?php echo $product_name; ?>
             </h2><hr/> 
     <div align="left" style="margin-left:24px; margin-right:24px; ">        
    <?php echo '<p style="color:red;" >'.$erro.'</p>'; ?>
    <?php if ($erro=='') {?>
        <table width="100%" border="0" cellspacing="0" cellpadding="10">
         
         <tr>
        <td width="16%" align="center" valign="top" scope="col"><img src="../p_images/<?php echo $imagem; ?>" width="200" height="200" class="top" /><br />
        <td width="32%" align="left" valign="top" scope="col">
        <form id="form1" name="form1" method="post" <?php if ($add==0) {
?>action="cart.php"<?php
} else {
?> action="ver_encomenda.php"<?php
}?>
            <p><?php if ($unit=='1'|| $peso==1) {
                        echo "<b>Quantidade Disponivel</b>"." - ".$quantidade." Unid.";
} else {
    echo "<b>Quantidade Disponivel</b>"." - ".$quantidade." kg";
}?> </p>
                     <br/>
                     <p><b>Preço</b></p>
      <p ><h2 style="color:orange"><?php if ($unit=='1') {
            echo $price." €/Unid.";
} else {
    echo $price." €/kg";
}?></h2></p><br/>
      <p><b>Quantidade Desejada</b>
      <br/>
    
    <?php if ($unit=='1' || $peso=='1') {?>
         <select name="quantity" id="quantity"> 
            <option value="1">1 </option> 
            <option value="2">2 </option> 
            <option value="3">3 </option> 
            <option value="4">4 </option> 
            <option value="5">5 </option>
        </select>
        <?php } else {?>
        <select name="quantity" id="quantity"> 
            <option value="0.1">0.100 </option>
            <option value="0.2">0.200 </option>
            <option value="0.3">0.300 </option>
            <option value="0.4">0.400 </option>
            <option value="0.5">0.500 </option>
            <option value="0.75">0.750 </option>
            <option selected value="1">1 </option> 
            <option value="1.5">1.5 </option> 
            <option value="2">2 </option> 
            <option value="2.5">2.5 </option>
            <option value="3">3 </option> 
            <option value="3.5">3.5 </option> 
            <option value="4">4 </option> 
            <option value="4.5">4.5 </option> 
            <option value="5">5 </option>
            <option value="10">10 </option>
            <option value="25">25 </option>
            <option value="50">50 </option>
            <option value="100">100 </option>
        </select> 
        <?php }     ?>

            <?php if ($unit=='1'|| $peso=='1') {
                    echo " Unid.";
} else {
    echo " kg";
}
        ?>
      </p>
      <p>
      <input type="hidden" name="total" id="total" value="<?php  echo $total;?>" />
        <input type="hidden" name="id_encomenda" id="id_encomenda" value="<?php echo $id_encomenda;?>" />
        <input type="hidden" name="id_produtor" id="id_produtor" value="<?php echo $id_produtor;?>" />
        <?php if ($peso=='0') {?>
        <input type="hidden" name="price" id="price" value="<?php echo $price;?>" />
        <?php } else {
    $price=0;?>
        <input type="hidden" name="price" id="price" value="<?php echo $price;?>" />
        <?php }?>
        <input type="hidden" name="pid" id="pid" value="<?php echo $pid;?>" /> 
        <input type="hidden" name="cat" id="cat" value="<?php echo $id_category;?>" /> <!-- Sends the category id for returning purpose -->
        <input type="hidden" name="quantidade" id="quantidade" value="<?php echo $quantidade;?>" />
        <input type="submit" name="button" id="button" value="Adicionar ao Cabaz" />
      </p>
  </form>
      
      
    </td>
      
    <td width="52%" align="left" valign="top" scope="col"><p><?php echo "<b>Produtor</b><br/>".$produtor." - ".$concelho; ?><br/></p>
        <?php if ($peso=='1') {
            echo "<hr><p><b>Info</b><br/>Produto vendido à unidade mas facturado ao peso.<br/>Necessário aferir o peso no <u>local de entrega</u>.</p>";
}?>
        <?php
        if ($cultura=="Biológico") {
            echo "<hr><p align='justify'><b>Produto - </b>Certificação Biológica<br/>A Agricultura Biológica é um modo de produção que visa produzir alimentos de elevada qualidade, através do uso adequado de métodos preventivos e culturais, tais como as rotações, os adubos verdes, a compostagem, e consociações.</p>";
        } elseif ($cultura=="Conversão-Bio") {
            echo "<hr><p align='justify'><b>Produto - </b>Conversão-Bio<br/> Agricultura convencional em transição gradual para agricultura Biológica.</p>";
        } elseif ($cultura=="Orgânico") {
            echo "<hr><p align='justify'><b>Produto - </b>Produto Orgânico<br/>Com base na informação prestada pelo produtor, estes produtos são sadios, cultivados sem agrotóxicos e sem fertilizantes químicos. 
Eles provêm de sistemas agrícolas baseados em processos naturais, que não agridem a natureza e mantêm a vida do solo intacta.
</p>";
        }
            
        ?>
        
      </td>
    </tr>
    <tr>
    <td colspan="3" align="left" valign="top" scope="col">
        <?php
        if ($details!='') {
            echo "<p align='justify'><b>Detalhes do Produto</b><br/>".$details."</p>";
        }  ?>  
     </td>
    </tr>
</table>
</div>
        
<?php } else {
    echo '<br/>Caso pretenda alterar a quantidade do produto em questão, deverá faze-lo em <b><u><a href="ver_encomenda.php">VER ENCOMENDA</a></u></b> ';
}?>
    
      </div>
      </div>
        <?php require_once("../includes/core/footer.php"); ?>
    </div>

</body></html>
