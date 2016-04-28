<?php 
//Página protegida ao admin
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
		 $link="<font color='orange'><b>Fechado  >> </b></font> <a href='bloquearAbrir.php'> Alterar estado</a>";
	 }else{
		 $link="<font color='orange'><b>Aberto >> </b></font><a href='bloquearAbrir.php'> Alterar estado</a>"; 
	 }
		 
?>

<?php 
	$nomeCliente=$_GET['n'];
?>

<?php 
//Buscar lista de produtos
$id_encomenda=$_GET['id'];
$produtos_list='';

	$sql=mysql_query("SELECT * FROM products, people, encomenda WHERE products.stock=1 AND encomenda.id_encomenda='$id_encomenda' AND products.id_produtor=people.id_people  ORDER BY product_name ASC");
	
		$produtos_list.='<option value="">'."Escolher...".'</option>';
	
		while($row=mysql_fetch_array($sql)){
			$pid=$row["id"];
			$product_name=$row['product_name'];
			$produtor=$row['name'];
			$price=$row['price'];
			$total=$row['total'];
			$id_produtor=$row['id_produtor'];
		
			//Select Dinamico
			$produtos_list.='<option value="'.$pid.' '.$price.' '. $id_produtor.'">'.$product_name.' -> '.$produtor.' -> '.$price.'</option>';
			
		}
?>

<?php 
//Parse the form data to the system
if(isset($_POST['produto']) && !empty($_POST['produto'])){
	$nomeCliente=$_GET['n'];
	if($_POST['quant']!=''){
	$id_encomenda=mysql_real_escape_string($_POST['id_encomenda']);
	$pidAndPriceAndProdutor=mysql_real_escape_string($_POST['produto']);
	$pieces=explode(" ", $pidAndPriceAndProdutor);
	$pid=$pieces[0];
	$price=$pieces[1];
	$id_produtor=$pieces[2];
	$quant=mysql_real_escape_string($_POST['quant']);
	$total=mysql_real_escape_string($_POST['total']);
	//$id_produtor=mysql_real_escape_string($_POST['id_produtor']);
	$priceTotal=$price*$quant;
	$NewCartTotal=$total+$priceTotal;

	// verificar se Produto existe na Encomenda
	$sql=mysql_query("SELECT * FROM encomenda_has_products WHERE id_produto=$pid AND id_encomenda='$id_encomenda' LIMIT 1");
	$existProd=mysql_num_rows($sql);
	if($existProd>0){
		$erro='Produto já existe na Encomenda Semanal!';
		$msg='Caso pretenda alterar a quantidade do produto em questão ou adicionar outro Produto, deverá ir a <u><a href="editar_encomenda_consumidor.php?id='.$id_encomenda.'">EDITAR ENCOMENDA</a></u> e alterar para a quantidade desejada ou adicionar outro Produto.';
	}else{

	
	//Adicionar Produto a Encomenda
		$q = $conDB->sql_query("UPDATE encomenda SET total='$NewCartTotal' WHERE id_encomenda='$id_encomenda'", BEGIN_TRANSACTION);
	
		$q = $conDB->sql_query("INSERT INTO
								encomenda_has_products(id_encomenda,id_produto,id_produtor,quant)
								VALUES('$id_encomenda','$pid','$id_produtor','$quant')", BEGIN_TRANSACTION);
	
		//Actualizar a Quantidade em Stock
		$sql=mysql_query("SELECT quantidade FROM products WHERE id=$pid LIMIT 1");
				while($row=mysql_fetch_array($sql)){
					$quantidade = $row['quantidade'];
				}
				$quantidade=$quantidade-$quant;
				
		$q = $conDB->sql_query("UPDATE products SET quantidade='$quantidade' WHERE id='$pid'", BEGIN_TRANSACTION);
						
		$q = $conDB->sql_query("",END_TRANSACTION);
		
		header("location:editar_encomenda_consumidor.php?id=$id_encomenda&n=$nomeCliente");
		exit();

}}else{
	$erro1='Escolher Produto.';
	$erro2='Preencher Quantidade Desejada.';
	}
}
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
								echo '<h1 ><a href="../storecostumers/ver_encomenda.php">VER ENCOMENDA</a></h1>';
							}else{
								echo '<h1 ><a href="../storecostumers/cart.php">O MEU CABAZ</a></h1>';
							}?>
								 	  <h1 ><a href="index.php">ADMIN</a></h1>
                                      <h1 ><a href="logout.php">SAIR</a></h1>
                    </div>
                </div>
            </div>
        </div>

        <div id="headerDrawerExpandable">
        <div id="headerDrawerContent">
        
       <ul class="horizontalMenu">
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
    		
    			<script>
       				$(function() {
						$( "input[type=submit]" )
						.button()
						$("editar").click(function( event ) {
						event.preventDefault();
          				});					
     				});
      		   </script>
    	 	<div style="width:220px;float:left; ">
        
    		<br/>
            <h3 style=" margin:0 10px;"><b>ENCOMENDA SEMANAL</b></h3><br/>
                <div  align="left" style="margin-left:10px ">
                <ul id="verticalmenu" class="glossymenu2">
                    <li style="padding: 15px 0;padding-left: 10px;"> CONSUMIDORES</li>
                    <li><a href="cabaz_semanal_produtores.php">PRODUTORES</a></li>
    			</ul>
              	</div>  
            <br/>
            <br/>
            <h3 style="margin:0 10px;"><b>ENCOMENDAS</b></h3><br/>
            	<div  align="left" style="margin-left:10px ">
		<ul id="verticalmenu" class="glossymenu2">
		<li><a href="total_transacoes.php">TOTAL TRANSAÇÕES</a></li>
		<li><a href="cabaz_semanal_consumidores_backup.php">CONSUMIDORES</a></li>
                <li><a href="cabaz_semanal_produtores_backup.php">PRODUTORES</a></li>
                <!--<li><a href="#">TOP PRODUTORES</a></li>
                <li><a href="#">TOP CONSUMIDORES</a></li>-->
                
		</ul>
                </div>
          </div>
          </div>
          <div style="width:780px;float:left;">
    	<div align="left" style="margin-left:15px ">
        	<br/>
            <h2>Adicionar Produto - <?php echo $nomeCliente; ?></h2><hr>
    		         
            <?php echo '<p style="color:red;" >'.@$erro.'</p>'; 
				  echo '<p>'.@$msg.'</p><br/>';
			?>
            <div align="left" style="margin-left:24px ">
            <?php if(@$erro==''){?>
           		<p>Produto:</p>
                <form action="editar_encomenda_consumidor_adicionar_produto.php?id=<?php echo $id_encomenda; ?>&n=<?php echo $nomeCliente; ?>" enctype="multipart/form-data" name="myForm" id="myForm" method="post">
                    <label for="produtos"></label><?php echo '<p style="color:red;" >'.@$erro1.'</p>';?>
                         <select name="produto" id="produto">
                         <?php echo $produtos_list; ?> 
                         </select>
                         <br/>
                         <br/>
                    <p>Quantidade Desejada:</p><?php echo '<p style="color:red;" >'.@$erro2.'</p>';?>
                     <input id="quant" name="quant" type="text" size="3"> Kg / Unid.
                     <br/>
                     <br/>
                     <input type="hidden" name="total" value="<?php echo $total; ?>"/>
                     <input type="hidden" name="id_encomenda" value="<?php echo $id_encomenda; ?>"/>
                     <input type="submit" name="button" id="button" value="Adicionar Produto" />
        		 </form>
          <?php }?>
            </div>
            
      </div>
	</div>
      <?php require_once("../includes/core/footer.php"); ?>
</div>
</body></html>
