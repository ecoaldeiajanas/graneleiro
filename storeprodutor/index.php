<?php
ob_start();
session_start();
require_once("../includes/core/header.php"); 
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
        	<li><a href="produtor_listar_produtos.php">Produtos</a></li> - 
            <li><a href="produtor_ver_encomendas.php">Encomenda da Semana</a></li>
        </ul>
   		</div>
    	</div>
 
        <div id="headerDrawer">
        </div>

            <div id="pageContainer">
            
      <div style="width:320px;float:left; ">
      <img src="../style/css/images/principles_menu.gif" width="300" height="300">
      </div>
      <div style="width:680px;float:left;">
      
	  
      <h2>Administração de Produtores </h2><hr>
      <p align="justify" style="margin-left:10px">
	  <?php echo "Olá ".$firstName."."; ?><br/>
      Bem Vindo(a) a página de administração de Produtores.<br/><br/>
      Nesta secção poderá inserir os seus produtos na página <a href="produtor_listar_produtos.php"><u>Produtos</u></a> e ver as encomendas na página <a href="produtor_ver_encomendas.php"><u>Encomendas da semana</u></a>.
      <br/><br/>
	    <a href="mailto:prossumidoresmadeira@gmail.com">prossumidoresmadeira@gmail.com</a></p>
      </div>
      </div>
            
            </div>
            
      <?php require_once("../includes/core/footer.php"); ?>
    </div>

</body></html>
