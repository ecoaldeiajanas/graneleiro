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
	 $sql=mysql_query("SELECT id_people, semana FROM encomenda WHERE id_people=$id AND semana=0  LIMIT 1");
		$existEnc=mysql_num_rows($sql);
?>


<?php 
////////////////////////////////////////////////////////////////////////////////////////////////////
//Run a Select Query to View service Items
////////////////////////////////////////////////////////////////////////////////////////////////////
$dynamicListS='';


$sql=mysql_query("SELECT * FROM servicos");
$servicoCount=mysql_num_rows($sql);
if($servicoCount>0){
	while($row=mysql_fetch_array($sql)){
		
		$id_servico=$row['id_servico'];
		$servico=$row['servico'];
		$nome=$row['nome'];
		$telef=$row['telef'];
		$email=$row['email'];
		$obs=$row['obs'];
		$imagem=$row['imagem'];
		$input="comprar";
		@$dynamicListS.='<table width="100%" border="0" cellspacing="0" cellpadding="0" >	
  <tr>
    <th colspan="3" scope="col" align="left"><p class="ui-widget-content ui-corner-all defaultText" style="color:#DEC05D; background:#556910; max-height:25px; min-height:25px; padding:3px"><b>'.$servico.'</b></p></th>
  </tr>
  <tr>
    <td width="16%" align="left" style="vertical-align:top" ><img class="top2" src="../p_images/'.$imagem.'" width="150" height="150" /></td>
    <td width="74%" align="left"><b>Nome: </b>'.$nome.'</br></br> <b>Telefone: </b>'.$telef.'</br></br><b>E-mail: </b> <a href="mailto:'.$email.'">'.$email.'</a></br></br><u><b>Descrição do serviço</b></u></br></br>'.$obs.' </td></br>
    
  </tr>
</table>';
		}@$i++;
}else{
	@$dynamicListTS="<br/>De momento não temos Serviços disponíveis.";
} 
//}//fechar block
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
            	<div class="heading">
		<ul>
		  <li><h1><a href="index.php">QUEM SOMOS</a></h1></li>
			    <?php if($existEnc>0){?>
			    						<li><h1><a href="#">STOCK DA SEMANA</a></h1></li>
				                    	
				                    <?php }else{?>
				                    <li><h1><a href="stock_semana.php?idCat=1">STOCK DA SEMANA</a></h1></li>
				                    <?php }?>
			   <li><h1><a href="servicos.php">SERVIÇOS</a></h1></li>
			   <li><h1><a href="info.php">Info</a></h1></li>
		</ul>
		</div>
            <div class="lastHeading">
                <div class="userOptions">
                     <div id="userOptionsInner">
                        	<?php if($existEnc==1){
								echo '<h1 ><a href="ver_encomenda.php">VER ENCOMENDA</a></h1>';
							}else{
								echo '<h1 ><a href="cart.php">O MEU CABAZ</a></h1>';
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
        <div id="headerDrawer">
        </div>
    <div id="pageContainer">
    <h2><p style="margin-left:13px;">Serviços</p></h2><hr>
    	<div id="productsww" style="width:900px;float:left;margin-left:50px; margin-right:50px;">
     	<?php echo $dynamicListS; ?>
       </div>
    
		
	 
      </div>
      
      <?php require_once("../includes/core/footer.php"); ?>
    </div>
    
</body></html>
