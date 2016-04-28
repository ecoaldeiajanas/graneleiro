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
//Error Reporting
error_reporting(E_ALL);
ini_set('display_errors','1');
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
//Delete item question to admin and delete product if they choose
if(isset($_GET['deleteid'])){
	echo 'Do you really want to delete product with ID of '.$_GET['deleteid'].'?<a href="inventory_list.php?yesdelete='.$_GET['deleteid'].'">Yes </a> | <a href="inventory_list.php">No</a>';
	exit();
}
if(isset($_GET['yesdelete'])){
	//Delete from data base
	$id_to_delete=$_GET['yesdelete'];
	$sql=mysql_query("DELETE FROM products WHERE id='$id_to_delete' LIMIT 1") or die (mysql_error());
	
}
?>

<?php 
//Parse the form data and add inventory item to the system
if(isset($_POST['local'])){
	$local=mysql_real_escape_string($_POST['local']);
	$date=mysql_real_escape_string(date('Y-m-d', strtotime($_POST['date'])));
	$hora=mysql_real_escape_string($_POST['hora']);

	//add this product into database now
	$sql=mysql_query("UPDATE delivery SET local='$local', date='$date', hora='$hora'") or die(mysql_error());
}
?>

<?php 
//Gather this product's full information automatically into the edit from below page
$delivery='';
	$sql=mysql_query("SELECT * FROM delivery ");
		while($row=mysql_fetch_array($sql)){
			$id=$row['id'];
			$local=$row['local'];
			$date=$row['date'];
			$hora=$row['hora'];
}
?>
 
<?php require_once("../includes/core/header.php"); ?> 

    <script>
   $(function() {
    $( "input[type=submit]" )
      .button()
      $("button").click(function( event ) {
        event.preventDefault();
      });
  });
   $(function() {
    $( "#date" ).datepicker();
  });
  </script>

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
        <h2>Local de Entrega</h2><hr>
			<div align="left" style="margin-left:10px ">
      			
                <br/>
                <div align="left" style="margin-left:24px ">
      			<?php echo "<b> Local: </b>".$local.' - ';
						echo "<b> Data: </b>".$date.' - ';
						echo "<b> Hora: </b>".$hora;
				?>
                </div>
		        <br/>      
    		<br/>
              	<div align="left" style=" width:405px;margin-left:24px ">
                <p><b>Novo Local de Entrega</b><hr></p><br/>
                
    			<form action="delivery_local.php" enctype="multipart/form-data" name="myForm" id="myForm" method="post">
                    Local:<br/>
                    	<input name="local" type="text"id="local" size="60" value="<?php echo $local; ?>"/>
                        <br/>
                        <br/>
                    Data:<br/>
                    	<input type="text" name="date" id="date" value="<?php echo $date; ?>"/></td>
                         <br/>
                         <br/>
                    Hora:<br/>
                    	 <input name="hora" type="text" id="hora" size="15" value="<?php echo $hora; ?>"/>
                         <br/>
                         <br/>
                    <input type="submit" name="button" id="button" value="Novo Local de Entrega"  /></td>
    			</form>
            <br />
            <br />
    		</div>      
		</div>
        </div>
      <?php require_once("../includes/core/footer.php"); ?>
</div>
</body></html>
