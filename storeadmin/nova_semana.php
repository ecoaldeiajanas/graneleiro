<?php 
//Pagina protegida ao ADMIN
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
//Error Reporting
error_reporting(E_ALL);
ini_set('display_errors','1');
?>
<?php
$ctr=0;
// Nova semana
if(isset($_GET['new'])){
	$ctr=1;
	$new=$_GET['new'];
	if($new==1){
	$msg="Foi criada uma nova semana e os dados foram guardados com sucesso!</br></br>
<form action='nova_semana.php?newDonativo' enctype='multipart/form-data' name='myForm' id='myForm' method='post'>
		Donativos da semana 
<input type='text' name='donativos' size='10px' style='max-height:30px; min-height:30px;' id='donativos'  title='por exemplo: 1.50' class='err_mandatory text ui-widget-content ui-corner-all defaultText' /> 
<input type='submit' name='comprar' id='comprar' value=' Inserir valor' /></br></br>
		</form>";
	}elseif($new==2){
	$msg="Não existem encomendas semanais!";
	}elseif($new==3){
	$msg="Foi criada uma nova semana e os dados foram guardados com sucesso!</br></br>Valor dos Donativos inserido com sucesso!";
	}
}
?>

<?php
$ctrD=0;
// Novo valor donativo
if(isset($_GET['newDonativo'])){
	$donativos=mysql_real_escape_string($_POST['donativos']);

	//add this product into database now
	$sql=mysql_query("UPDATE grupo SET donativos='$donativos'") or die(mysql_error());

	header("location:../storeadmin/nova_semana.php?new=3");
		exit();
}
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
//Guardar encomendas da semana e criar nova semana
if(isset($_GET['newSemana'])){
	
	//Buscar lista de produtores
	
	$q = $conDB->sql_query("SELECT DISTINCT id_produtor FROM  (select  encomenda_has_products.id_encomenda,encomenda_has_products.id_produtor, encomenda.semana FROM encomenda, encomenda_has_products where encomenda.id_encomenda=encomenda_has_products.id_encomenda AND encomenda.semana=0) T");
		
		$existProdutor=mysql_num_rows($q);

		if($existProdutor>0){
	
		while($r = $conDB->sql_fetchrow($q)) {
		
		foreach ($r as $key => $value) {
		$dynamicList='';
		$cartTotal=0;
		$email='';
		$name='';
			$sql=mysql_query("SELECT email, name FROM people WHERE id_people='$value'");
				while($row=mysql_fetch_array($sql)){
				    $email=$row['email'];
				    $name=$row['name'];
				}

			$sql1=mysql_query("SELECT products.id, products.product_name, products.price, SUM(encomenda_has_products.quant ) as quant, SUM(products.price*encomenda_has_products.quant) as priceTotal, encomenda_has_products.id_produtor, semana FROM encomenda_has_products, people, products, encomenda WHERE encomenda_has_products.id_produtor=people.id_people
AND encomenda_has_products.id_produto=products.id 
AND encomenda_has_products.id_encomenda=encomenda.id_encomenda
AND encomenda.semana=0
AND people.id_people='$value' GROUP BY products.id ");

			while($row=mysql_fetch_array($sql1)){
			$pid=$row['id'];
			$id_produtor=$row["id_produtor"];
			$quant=$row["quant"];
			$product_name=$row['product_name'];
			$price=$row['price'];
			$unit=$row['unit'];
			$priceTotal=$price*$quant;
			$cartTotal=$priceTotal+$cartTotal;

			  
		  
		  	}//while			
			

			///////////////////////////////////////////////////////////////////////////////
			//Inserir dados na tabela encomendaProdutor
			$q1 = $conDB->sql_query("INSERT INTO encomendaprodutor(id_people,total,date)
				VALUES('$value','$cartTotal', now())", @BEGIN_TRANSACTION) ;
				$id_encomendaProdutor=mysql_insert_id();
	

			//buscar todos os valores da encomenda do produtor X
			$sq12=mysql_query("SELECT  id_produto, quant, semana FROM encomenda_has_products, encomenda WHERE encomenda_has_products.id_produtor='$value' 
AND encomenda_has_products.id_encomenda=encomenda.id_encomenda
AND encomenda.semana=0 ");
			

			while($row=mysql_fetch_array($sq12)){
			$id_produtor2=$row['id_produtor'];
			$id_produto2=$row['id_produto'];
			$quant2=$row['quant'];
	
			//Inserir dados na tabela encomendaprodutor_has_products	
			$q1 = $conDB->sql_query("INSERT INTO encomendaprodutor_has_products(id_encomendaProdutor,id_produto,id_produtor,quant)
				VALUES('$id_encomendaProdutor', '$id_produto2', '$value','$quant2')", @BEGIN_TRANSACTION) ;
	
	
			}//while
			$q1 = $conDB->sql_query("",@END_TRANSACTION);
			
			

		}//foreach
	}//while
	
	/////////////////////////////////////////////////////////////////////////////////////////
	// Calcular receita da sustentabilidade do grupo
	$sql=mysql_query("SELECT SUM(total) as totalEncomendas FROM encomenda WHERE semana=0");
				while($row=mysql_fetch_array($sql)){
				    $totalEncomendas=$row['totalEncomendas'];   
				}
					$totalEncomendasTaxa=$totalEncomendas+$totalEncomendas*10/100;
					$valorSustentabilidade=$totalEncomendasTaxa-$totalEncomendas;

	//actualizar valor da sustentabilidade na base de dados
	$q = $conDB->sql_query("UPDATE grupo SET sustentabilidade='$valorSustentabilidade'",@BEGIN_TRANSACTION);
	$q = $conDB->sql_query("",@END_TRANSACTION);

	////////////////////////////////////////////////////////////////////////////////////////////
	//Alterar semana da encomenda (0=Encomenda da semana / 1=Encomenda passada
	
	$q = $conDB->sql_query("UPDATE encomenda SET semana=1 WHERE semana=0", @BEGIN_TRANSACTION);
	$q = $conDB->sql_query("",@END_TRANSACTION);

	

	header("location:../storeadmin/nova_semana.php?new=1");
		exit();
}else{
	header("location:../storeadmin/nova_semana.php?new=2");
		exit();
}
}
?>

<?php 
//Verificar se existe encomenda da semana
$id_people=$_SESSION["id"];

	 $sql=mysql_query("SELECT id_people, semana FROM encomenda WHERE id_people=$id_people AND semana=0  LIMIT 1");
		$existEnc=mysql_num_rows($sql);
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
      $("comprar").click(function( event ) {
        event.preventDefault();
      });	 
	 
  });
  </script>
    <h2>Nova Semana</h2><hr>
    <br/>

   <?php
	

   		if($ctr==0){
   			echo 'Deseja guardar os dados da Semana e criar uma nova semana? <a href="nova_semana.php?newSemana">  Sim </a> | <a href="../storeadmin/index.php">Não</a>';
		}else{ 
         	echo $msg;
		}
   ?>
      </div>
      
      <?php require_once("../includes/core/footer.php"); ?>
    </div>

</body></html>


<?php 

?>
