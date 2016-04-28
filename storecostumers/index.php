<?php
ini_set('display_errors', 0); 
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
//Verificar local de Entrega de Produtos
	$sql=mysql_query("SELECT * FROM delivery");
		while($row=mysql_fetch_array($sql)){
			$local=$row['local'];
			$date=$row['date'];
			$hora=$row['hora'];
		}
?>

<?php
//dados de sustentabilidade
  $sustentabilidade=0;
	$sql=mysql_query("SELECT total FROM encomenda WHERE id_people=$id AND semana=0");
		while($row=mysql_fetch_array($sql)){
		$sustentabilidade+=$row['total'];
		$donativos=0;
		}
    
    // 10% do total
    $sustentabilidade = $sustentabilidade * 0.1;
?>

<?php 
////////////////////////////////////////////////////////////////////////////////////////////////////
//Run a Select Query to View Info Items
////////////////////////////////////////////////////////////////////////////////////////////////////
$dynamicListInfo='';


$sql=mysql_query("SELECT * FROM info ORDER BY data DESC LIMIT 1");
$servicoCount=mysql_num_rows($sql);
if($servicoCount>0){
	while($row=mysql_fetch_array($sql)){
		
		$id_info=$row['id_info'];
		$titulo=$row['titulo'];
		$texto=$row['texto'];
		$data=$row['data'];
		
		@$dynamicListInfo.='
<table width="100%" >
	
  <tr>   
    <td align="left"><p class="ui-widget-content ui-corner-all defaultText" style="color:#DEC05D;max-height:25px; min-height:25px; padding:3px; background:#556910;"><b>'.$titulo.'</b>
<p class="ui-widget-content ui-corner-all defaultText" align="justify"  style="padding:10px;" ">'.$texto.'</p></td>
    
  
 
 
  </tr>
</thead>
</table>';
		}@$i++;
}else{
	@$dynamicListInfo="<br/>De momento não temos Informações disponíveis.";
} 
//}//fechar block
//mysqli_close($conDB);
?>

<body>

 <div id="motherContainer">
        <div id="header">
            <div id="smalllogo"></div>

	    <div class="heading">
	    <ul>
  	    <li><h1><a href="index.php">QUEM SOMOS</a></h1></li>
            <?php if($existEnc>0){?>
            <li><h1><a href="#">STOCK DA SEMANA</a></h1></li>
                 <?php }else{?>
                 <li><h1><a href="stock_semana.php">STOCK DA SEMANA</a></h1></li>
                 <?php }?>
           <li><h1><a href="servicos.php">SERVIÇOS</a></h1></li>
	   <li><h1><a href="info.php">Info</a></h1></li>
           </ul>
           </div>

            <div class="lastHeading">
                <div class="userOptions">
                    <div id="userOptionsInner">
                        	<?php if($existEnc==1){
								echo '<h1><a href="ver_encomenda.php">VER ENCOMENDA</a></h1>';
							}else{
								echo '<h1><a href="cart.php">O MEU CABAZ</a></h1>';
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

      <div style="width:230px; height:470px;float:left; ">
      <p><img  src="../style/css/images/principles_menu.gif" width="188" height="470" align="left"></p>
<p align="left"><font size="1"><a href="mailto:graneleiro@ecoaldeiajanas.org">graneleiro@ecoaldeiajanas.org</a></font></p>
      </div>
      <div style="width:680px;float:left; ">
      
	  <p align="justify" style=" padding-left:10px;">

	  <?php echo "Viva ".$firstName."."; ?><br/>
	<div style="width:450px;float:left; ">  
      <p style="padding:10px;" class="ui-widget-content ui-corner-all defaultText">As Encomendas apenas são permitidas entre Domingo e Quarta-feira de cada semana.<br/><br/><b>Local de entrega</b><br/>
      <?php echo $local. "<br/> dia ".$date. " entre as ".$hora."." ?>
	  <br></br>
	  <br><b>Como funciona?</b></br>
<br>- <b>Encomendas e abertura da plataforma</b> | 2ª das 8h00 a 4ª Feira às 00h00</br>
<br>- <b>Envio das listas aos produtores</b> 5ª Feira manhã</br>
<br>- <b>Entrega dos produtores</b> 5ª Feira ou 6ª Feira manhã</br>
<br>- <b>Levantamento das Encomendas em Janas</b> | entre 6ª Feira 14h - 19h ou Sábado 10h às 18h</br>
<br>- <b>Entregas em Lisboa | 6ª Feira à tarde </b>(contacta para mais info)</br>
<br>Nota importante: produtos da Herdade do Freixo do Meio devem ser encomendados até 4ª feira às 11h30 (manhã)</br></p></div>
      <div style=" width:220px; float:right;" align="center">
      <p class="ui-widget-content ui-corner-all defaultText" style="width:220px;color:#DEC05D;max-height:25px; min-height:25px; padding:5px; background:#556910;"  ><b>Comissão de 10%</b></p>
      <p class="ui-widget-content ui-corner-all defaultText"> <?php echo number_format($sustentabilidade,2)." - Euros" ;?></p>
      <p class="ui-widget-content ui-corner-all defaultText" style="width:220px;color:#DEC05D;max-height:25px; min-height:25px; padding:5px; background:#556910;"  ><b>Donativos</b></p>
      <p class="ui-widget-content ui-corner-all defaultText"> <?php echo number_format($donativos,2)." - Euros" ;?></p>
      <p align="center">Valores da última semana</p>
      </div>
      
	<br></br>
	<br></br>
	<br></br>
	<br></br>
	<br></br>
	<br></br>
	<p align="center"><?php echo $dynamicListInfo; ?></p><br/>
	    
      </div>
      </div>
	
      <?php require_once("../includes/core/footer.php"); ?>
    </div>
 
</body></html>
