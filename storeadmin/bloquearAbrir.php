<?php 
ob_start();
session_start();
require_once("../storescripts/connectMysql.php");
 $logged = ($_SESSION["id"] && $_SESSION["id"]!=null);
    if($logged){
        $id = $_SESSION["id"];
        $name = $_SESSION["name"];
        $firstName = explode(" ",$name); $firstName = $firstName[0];
        $admin = $_SESSION["manager"];
	$produtor = $_SESSION["produtor"];
    	}else{
	header("location:../login.php");
	exit();
	}    
?> 

<?php 
//Verificar se existe encomenda da semana
$id_people=$_SESSION["id"];

	 $sql=mysql_query("SELECT id_people, semana FROM encomenda WHERE id_people=$id_people AND semana=0  LIMIT 1");
		$existEnc=mysql_num_rows($sql);
?>

<?php 
//Verificar se site está bloqueado
	 $sql=mysql_query("SELECT * FROM block LIMIT 1");
	 while($row=mysql_fetch_array($sql)){
		 $block=$row['block'];
	 }
	 if($block==0){
		 $msg="O website está <b> Fechado</b>. Deseja alterar o estado para <a href='bloquearAbrir.php?alt=1'> <u>Aberto</u> </a>?";
		 $link="<font color='orange'><b>Fechado  >> </b></font> <a href='bloquearAbrir.php'> Alterar estado</a>";
	 }else{
		  $msg='O website está <b> Aberto</b>. Deseja alterar o estado para <a href="bloquearAbrir.php?alt=0"> <u>Fechado</u> </a>?';
		  $link="<font color='orange'><b>Aberto >> </b></font><a href='bloquearAbrir.php'> Alterar estado</a>";  
	 }
		 
?>

<?php
//Alterar estado do site
if(isset($_GET['alt'])){
	$estado=$_GET['alt'];
	$sql=mysql_query( "UPDATE block SET  block='$estado'")or die(mysql_error());
	header("location:bloquearAbrir.php");
		exit();
}
?>

<?php
//enviar email a cada produtor
$emailMsg='';
if(isset($_GET['email'])){
	$emailEstado=$_GET['email'];
	if($emailEstado==1){
		
	
	//Buscar lista de produtores
	
	$q = $conDB->sql_query("SELECT DISTINCT id_produtor FROM  (select  encomenda_has_products.id_encomenda,encomenda_has_products.id_produtor, encomenda.semana FROM encomenda, encomenda_has_products where encomenda.id_encomenda=encomenda_has_products.id_encomenda AND encomenda.semana=0) T ");
			
		while($r = $conDB->sql_fetchrow($q)) {
		
		foreach ($r as $key => $value) {
		$dynamicList='';
		$cartTotal=0;
		$email='';
		$name='';
			$sql=mysql_query("SELECT email, name FROM people WHERE id_people='$value'");
				while($row=mysql_fetch_array($sql)){
				    $emailP=$row['email'];
				    $nameP=$row['name'];
				}

			$sql1=mysql_query("SELECT products.id, products.product_name, products.price, SUM(encomenda_has_products.quant ) as quant, SUM(products.price*encomenda_has_products.quant) as priceTotal, encomenda_has_products.id_produtor, encomenda.semana, encomenda.id_encomenda FROM encomenda, encomenda_has_products, people, products WHERE encomenda_has_products.id_produtor=people.id_people
AND encomenda_has_products.id_produto=products.id  AND people.id_people='$value' AND encomenda.semana=0 AND encomenda.id_encomenda=encomenda_has_products.id_encomenda GROUP BY products.id ");

			while($row=mysql_fetch_array($sql1)){
			$pid=$row['id'];
			$id_produtor=$row["id_produtor"];
			$quant=$row["quant"];
			$product_name=$row['product_name'];
			$price=$row['price'];
			$unit=$row['unit'];
			$priceTotal=$price*$quant;
			$cartTotal=$priceTotal+$cartTotal;

			  //Lista Dinamica
			  $dynamicList.='<tr>';
			  $dynamicList.='<td class="cart">'.$product_name.' </td>';
			  $dynamicList.='<td align="center" class="cart">'.$price.' €</td>';
			  if($unit==1){
			  $dynamicList.='<td align="center" class="cart">'.$quant.' Unid.</th>';
			  }else{
			  $dynamicList.='<td align="center" class="cart">'.$quant.' Kg</th>';
			  }
			  $dynamicList.='<td align="center" class="cart">'.number_format($priceTotal, 2).' €</th>';			  
		  	  $dynamicList.='</tr><br/>';
		  
		  	}//while

			
			///////////////////////////////////////////////////////////////////////////////
			//email para o Produtor
	
			$ownerEmail='graneleiro@ecoaldeiajanas.org';
			$array=(explode(" ",$nameP));
			$firstNameP=$array[0];
	
			$to = $emailP;
			//$to='ajcfgomes@hotmail.com';
	
			$subject = 'Encomenda de produtos.';
	
			$headers = "From: Prossumidores  <" . strip_tags($ownerEmail) . ">\n";
			$headers .= "Reply-To: ". strip_tags($ownerEmail) . "\r\n";
			//$headers .= "CC: susan@example.com\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=utf-8\r\n";
	
			$message = '<html><body>';
			$message .='<p>Viva '.$firstNameP.'!</p>';
			$message .='<p>Produtos encomendados esta semana.</p>';
			$message .= '<table width="80%" border="1" cellspacing="2" cellpadding="0">';
			$message .=' <tr>';
			$message .='<th width="18%" class="prod" align="left" bgcolor="#c5cda8" scope="col"><b>Produto</b></th>';
			$message .='<th width="13%" class="prod" bgcolor="#c5cda8" scope="col"><b>Preço Unit.</b></th>';
			$message .='<th width="13%" class="prod" align="center" bgcolor="#c5cda8" scope="col"><b>Quantidade</b></th>';
			$message .='<th width="11%" class="prod" bgcolor="#c5cda8" scope="col"><b>Total</b></th>';
			$message .='</tr>'; 
			$message .=  '<tr>'.$dynamicList.'</tr>';
			$message .= '</table>';
			$message .='<br/><p align="left"><b>Total dos produtos encomendados: '.number_format($cartTotal, 2).' - euros </b></p>';
			$message .= '</body></html>'; 
	
			mail($to, $subject, $message, $headers);
	
			//header("location:bloquearAbrir.php?email=1");
			//exit();
			///////////////////////////////////////////////////////////////////////////////
			//Inserir dados na tabela encomendaProdutor
			/*$query = $conDB->sql_query("INSERT INTO encomendaprodutor(id_people,total,date)
				VALUES('$value','$cartTotal', now())", BEGIN_TRANSACTION) ;
				$id_encomendaProdutor=mysql_insert_id();
	
			$sql1=mysql_query("SELECT id_produtor, id_produto, quant FROM encomenda_has_products WHERE id_produtor='$value'");
			while($row=mysql_fetch_array($sql1)){
			
			$id_produtor2=$row['id_produtor'];
			$id_produto2=$row['id_produto'];
			$quant2=$row['quant'];
	
			//Inserir dados na tabela encomendaprodutor_has_products	
			$query = $conDB->sql_query("INSERT INTO encomendaprodutor_has_products(id_encomendaProdutor,id_produto,id_produtor,quant)
				VALUES('$id_encomendaProdutor', '$id_produto2', '$id_produtor2','$quant2')", BEGIN_TRANSACTION) ;
	
	
			}//while
			$query = $conDB->sql_query("",END_TRANSACTION);*/

			

		}//foreach
	}//while
$emailMsg="<br/><br/><li><u><b>Email enviado com sucesso!</b></u></li>";
}
}//isset email
?>
  
<?php require_once("../includes/core/header.php"); ?> 

    <div id="motherContainer">
        <div id="header">
            <div id="smalllogo"></div>
		<div class="heading">
		<ul>
		  <li><h1><a href="../storecostumers/index.php">QUEM SOMOS</a></h1></li>
			  <?php if($existEnc>0){?>
	          <li><h1><a href="#">STOCK DA SEMANA</a></h1></li>
			  <?php }else{?>
	                  <li><h1><a href="../storecostumers/stock_semana.php?idCat=1">STOCK DA SEMANA</a></h1></li>
		          <?php }?>
	          <li><h1><a href="../storecostumers/servicos.php">SERVIÇOS</a></h1></li>
	          <li><h1><a href="../storecostumers/info.php">Info</a></h1></li>
		</ul>
		</div>
            <div class="lastHeading">
                <div class="userOptions">
                    <div id="userOptionsInner">
                        	<?php if($existEnc==1){
								echo '<h1><a href="../storecostumers/ver_encomenda.php">VER ENCOMENDA</a></h1>';
							}else{
								echo '<h1><a href="../storecostumers/cart.php">O MEU CABAZ</a></h1>';
							}?>
                            <?php if($admin){
								 echo '<h1 ><a href="../storeadmin/index.php">ADMIN</a></h1>';}?>
                            <?php if($produtor){
								 echo '<h1 ><a href="../storeprodutor/index.php">PRODUTOR</a></h1>';}?>
                                      <h1 ><a href="logout.php">SAIR</a></h1>
                    </div>
                </div>
            </div>
        </div>

        <div id="headerDrawerExpandable">
        <div id="headerDrawerContent">
        
        <ul class="horizontalMenu">
            <!--<li><a href="bloquearAbrir.php">Bloquear/Abrir - Site</a></li>-->
	    <li><?php echo $link; ?></li>
            <li><a href="nova_semana.php">Nova Semana</a></li>
            <li><a href="nova_categoria.php">Categorias</a></li>
            <li><a href="listar_produtos.php">Produtos</a></li>
            <li><a href="delivery_local.php">Local de Entrega</a></li>
            <li><a href="encomendas.php">Encomendas</a></li>
	    <li><a href="pessoas.php">Pessoas</a></li>
	    <li><a href="novo_servico.php">Serviços</a></li>	    
            <li><a href="novo_info.php">Info</a></li>
        </ul>

   	</div>
    	</div>
 
        <div id="headerDrawer">
        </div>

        <div id="pageContainer">

	 <h2>Alterar estado</h2><hr>
   	  <?php echo '<br>'.$msg; ?>
	 <?php  if(@$emailEstado==1){
		echo $emailMsg;
		}else{	
		echo '<br/><br/><li><u><a href="bloquearAbrir.php?email=1">Enviar email para os produtores com os produtos encomendados da semana.</a></u></li>'; 
	        }
	 ?> 	
	 <?php //echo $dynamicList;?> 
  
       </div>
      <?php require_once("../includes/core/footer.php"); ?>
    </div>

</body></html>
