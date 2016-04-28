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
//Id encomenda e nome do cliente
if(isset($_GET['id'])){
	$targetID=$_GET['id'];
	$nomeCliente=$_GET['n'];
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

<?php require_once("../includes/core/header.php"); require_once("../includes/dialogs.php"); ?> 
<link href="../style/css/include/demo_table_jui.css" rel="stylesheet" type="text/css"/>
<link href="../style/css/include/demo_page.css" rel="stylesheet" type="text/css"/>

<!--///////////////////////////////////////////////////////////////////////////////-->
<script type="text/javascript"> 
// Função para desactivar a tecla enter
	function stopRKey(evt) { 
	  var evt = (evt) ? evt : ((event) ? event : null); 
	  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null); 
	  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;} 
	} 
document.onkeypress = stopRKey; 
</script>

<script>
$(function() {
var x = <?php echo $targetID; ?>;
	// build table
	$('#EditarCabazConsumidores').dataTable( {
			"oLanguage": {
         	"sLoadingRecords": "Não existem Produtos."
      		 },
			"bFilter": false,
			"bJQueryUI": true,
	        "sPaginationType": "full_numbers",
			"bPaginate": true,
			"bLengthChange": true,
	        //"bProcessing": true,
	        "iDisplayLength": 10,
		"aaSorting": [[ 1, "asc" ]],
			//"bInfo": false,
	        "sAjaxSource": "../ajax/aj_getEditarCabazConsumidores_backup.php?id="+x,
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
    },
			 "fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
            /*
             * Calculate the total market share for all browsers in this table (ie inc. outside
             * the pagination)
             */
            var iTotalMarket = 0;
            for ( var i=0 ; i<aaData.length ; i++ )
            {
                iTotalMarket += aaData[i][5]*1;
            }
		//taxa de 10%
		iTotalMarket10=iTotalMarket+iTotalMarket*10/100;

			document.getElementById("total").innerHTML='<b>Total da Encomenda: '+ iTotalMarket.toFixed(2) +' - Euros </b>';
			document.getElementById("totalTaxa").innerHTML='<b>Total da Encomenda+Taxa: <font style="color:red">'+ iTotalMarket10.toFixed(2) +' - Euros </b></font>';
			
        }
		});
		// build the top bar on the table
    $( ".nvBut" ).button({
    	icons: {
            primary: "ui-icon-circle-plus"
        },
        text: true
    }).click(function() {
			var y = '<?php echo $nomeCliente; ?>';
			self.location='editar_encomenda_consumidor_adicionar_produto.php?id=' + x+'&n='+y;
		
	});

	$( ".rtBut" ).button({
    	icons: {
            primary: "ui-icon-arrowreturnthick-1-w"
        },
        text: true
    }).click(function() {
		
		self.location='cabaz_semanal_consumidores_backup.php';
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
            <button id="returnConsumidor" class="rtBut">CONSUMIDORES</button>
			</div>
		 
    			
    	 	<div style="width:220px;height:400px;float:left; ">
            </br>
    	 	  <h3 style="color:#360; margin:0 10px;"><b>ENCOMENDA SEMANAL</b></h3><br/>
                <div  align="left" style="margin-left:10px ">
                <ul id="verticalmenu" class="glossymenu2">
                    <li><a href="cabaz_semanal_consumidores.php">CONSUMIDORES</a></li>
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
          <div style="width:780px;float:left;padding-left:15px;">
    		<br />
            <h2>Encomenda do Consumidor - <?php echo $nomeCliente; ?></h2><hr>
            <div align="left">
             <table cellpadding="0" cellspacing="0" border="0" class="display" id="EditarCabazConsumidores" width="100%">
   				 <thead>
        		 <tr>
                 	<th width="5%">#</th>
                    <th align="left" width="30%">Produto</th>
                    <th align="left" width="25%">Produtor</th>
                    <th width="15%">Preço Unit.</th>
                    <th width="10%">Quantidade</th>
                    <th width="15%">Preço Total</th>
                    
       		     </tr>
    			 </thead>
    			 <tfoot>
                 <tr>
		    <th>#</th>
                    <th align="left">Produto</th>
                    <th align="left">Produtor</th>
                    <th>Preço Unit.</th>
                    <th>Quantidade</th>
                    <th>Preço Total</th>
        		</tr>
    			</tfoot>
			  </table>
            </br>
    			<p id="total"> </p>
			<p id="totalTaxa"> </p>
			<p></br>* Ao preço total da encomenda está indexada uma taxa de 10% que ajudará na sustentabilidade do sistema.</p>
      </div>
	</div>
      <?php require_once("../includes/core/footer.php"); ?>
</div>
</body></html>
