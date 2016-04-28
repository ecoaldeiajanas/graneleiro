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

<?php require_once("../includes/core/header.php"); ?> 
<link href="../style/css/include/demo_table_jui.css" rel="stylesheet" type="text/css"/>
<link href="../style/css/include/demo_page.css" rel="stylesheet" type="text/css"/>

<script>
$(function() {

	// build table
	$('#CabazConsumidores').dataTable( {
		"oLanguage": {
         	"sLoadingRecords": "Não existem Encomendas."
      		 },
			"bFilter": false,
			
			"bJQueryUI": true,
	        "sPaginationType": "full_numbers",
			"bPaginate": true,
			"bLengthChange": true,
	        //"bProcessing": true,
	        "iDisplayLength": 10,
		"aaSorting": [[ 3, "desc" ]],
	        "sAjaxSource": "../ajax/aj_getCabazConsumidores_backup.php",
	        "fnInitComplete": function(oSettings, json) {
		    	//correctDataTable();
		    },
			"fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
				correctDataTable();
				  oSettings.jqXHR = $.ajax( {
					"dataType": 'json',
					"type": "POST",
					"url": sSource,
					"data": aoData,
					"success": fnCallback
		
      } );
    }
		});	

	$( ".edBut" ).button({
    	icons: {
            primary: "ui-icon-wrench"
        },
        text: true
    }).click(function() {
		var anSelected 	= fnGetSelected( "#CabazConsumidores" );
    	if ( anSelected.length == 1 ) {
    		var id_encomenda = anSelected.find("td").first().html();
	    	var name = anSelected.find("td").first().next().html();
			var total = anSelected.find("td").first().next().next().html();
			
			self.location='ver_encomenda_consumidor_backup.php?id=' + id_encomenda + '&n=' + name;
		}
	});
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
    		<div class="navBar">
            <button id="editPessoaBut" class="edBut">Ver Encomenda</button>
			</div>
            
        
		</br>
       	<div style="width:220px;float:left;">
       	  <h3 style=" margin:0 10px;"><b>ENCOMENDA SEMANAL</b></h3><br/>
                <div  align="left" style="margin-left:10px ">
                <ul id="verticalmenu" class="glossymenu2">
                   <ul> 
                   <li><a href="cabaz_semanal_consumidores.php"> CONSUMIDORES</a></li>
                   </ul>
                   <ul>
                   <li><a href="cabaz_semanal_produtores.php">PRODUTORES</a></li>
                   </ul>
              </div> 
               
            <br/>
            <br/>
            <h3 style="margin:0 10px;"><b>ENCOMENDAS</b></h3><br/>
            	<div  align="left" style="margin-left:10px ">
		<ul id="verticalmenu" class="glossymenu2">
		<li><a href="total_transacoes.php">TOTAL TRANSAÇÕES</a></li>
		<li style="padding: 15px 0;padding-left: 10px;">CONSUMIDORES</li>
                <li><a href="cabaz_semanal_produtores_backup.php">PRODUTORES</a></li>
		<li><a href="limparbackup.php">REMOVER REGISTOS</a></li>
                <!--<li><a href="#">TOP PRODUTORES</a></li>
                <li><a href="#">TOP CONSUMIDORES</a></li>-->
                
		</ul>
                </div>
          </div>
          </div>
          <div style="width:780px;float:left; padding-left:15px">
          <h2>Encomendas - Consumidores</h2><hr>
          		<div align="left" >

               <table cellpadding="0" cellspacing="0" border="0" class="display" id="CabazConsumidores" width="100%">
   				 <thead>
        		 <tr>
                    <th width="5%">#</th>
                    <th align="left" width="65%">Consumidor</th>
                    <th width="15%">Total</th>
                    <th width="15%">Data</th>
       		     </tr>
    			 </thead>
    			 <tfoot>
                 <tr>
                    <th>#</th>
                    <th align="left">Consumidor</th>
                    <th>Total</th>
                    <th>Data</th>
        		</tr>
    			</tfoot>
			  </table>
            </div>
			<p></br>* Ao preço total da encomenda está indexada uma taxa de 10% que ajudará na sustentabilidade do sistema.</p>
     </div>
      <?php require_once("../includes/core/footer.php"); ?>
</div>

</body></html>
