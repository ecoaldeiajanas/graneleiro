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
//Run a Select Query to View Info Items
////////////////////////////////////////////////////////////////////////////////////////////////////
$dynamicListInfo='';


$sql=mysql_query("SELECT * FROM info ORDER BY data DESC");
$servicoCount=mysql_num_rows($sql);
if($servicoCount>0){
	while($row=mysql_fetch_array($sql)){
		
		$id_info=$row['id_info'];
		$titulo=$row['titulo'];
		$texto=$row['texto'];
		$data=$row['data'];
		
		@$dynamicListInfo.='<table width="100%" >	
  <tr>
    <p align="right">'.$data.'</p>
    <td align="left"><p class="ui-widget-content ui-corner-all defaultText" style="color:#DEC05D;max-height:25px; min-height:25px; padding:3px; background:#556910;"><b>'.$titulo.'</b>
	<p class="ui-widget-content ui-corner-all defaultText" align="justify"  style="padding:10px" ">'.$texto.'</p></td>
    
  
  </tr>

</table></br>';
		}@$i++;
}else{
	@$dynamicListInfo="<br/>De momento não temos Informações disponíveis.";
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
    <h2><p style="margin-left:13px;">Informação</p></h2><hr>
    	<div id="productsww" style="width:900px;float:left;margin-left:50px; margin-right:50px;">
     	<?php echo $dynamicListInfo; ?>
       </div>
    
		
	 
      </div>
      
      <?php require_once("../includes/core/footer.php"); ?>
    </div>
    
</body></html>
