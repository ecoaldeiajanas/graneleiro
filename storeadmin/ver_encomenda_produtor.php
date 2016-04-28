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
if(isset($_GET['pid'])){
	//id Produtor
	$targetID=$_GET['pid'];
	//nome Produtor
	$name=$_GET['n'];
	//valor total da Encomenda do Produtor
	$cartTotal=$_GET['t'];
}
?>	


<?php require_once("../includes/core/header.php"); ?> 
<link href="../style/css/include/demo_table_jui.css" rel="stylesheet" type="text/css"/>
<link href="../style/css/include/demo_page.css" rel="stylesheet" type="text/css"/>

<!--///////////////////////////////////////////////////////////////////////////////-->
<script>
$(function() {
var x = <?php echo $targetID; ?>;
	// build table
	$('#VerEncProdutores').dataTable( {
			"bFilter": false,
			"bJQueryUI": true,
	        "sPaginationType": "full_numbers",
			"bPaginate": true,
			"bLengthChange": true,
	        "bProcessing": true,
	        "iDisplayLength": 10,
		"aaSorting": [[ 1, "asc" ]],
			//"bInfo": false,
	        "sAjaxSource": "../ajax/aj_getVerEncProdutores.php?id="+x,
	        "fnInitComplete": function(oSettings, json) {
		    	correctDataTable();
		    }
		});

	$( ".rtBut" ).button({
    	icons: {
            primary: "ui-icon-arrowreturnthick-1-w"
        },
        text: true
    	}).click(function() {
		
		self.location='cabaz_semanal_produtores.php';
	});
	
	$( ".detailsBut" ).button({
    	icons: {
            primary: "ui-icon-extlink"
        },
        text: true
    	}).click(function() {
		var anSelected 	= fnGetSelected( "#VerEncProdutores" );
    		if ( anSelected.length == 1 ) {
    		var id_produto = anSelected.find("td").first().html();
		var product_name = anSelected.find("td").first().next().html();
		$( "#dialogProduto-form" ).dialog( "option", "title", "Produto - "+product_name  )
			.dialog( "open" )
			
	}
		
	});
	
	
	// dialog nova pessoa
	$( "#dialogProduto-form" ).dialog({
		autoOpen: false,
		width:500,
		resizable: true,
		modal: true,
		position:['middle',100],
		open: function(event, ui){
				
		var anSelected 	= fnGetSelected( "#VerEncProdutores" );
    		if ( anSelected.length == 1 ) {
    		var id_produto = anSelected.find("td").first().html();
		}
		// build table
	$('#detailProd').dataTable( {
			"bFilter": false,
			"bJQueryUI": true,
	        	"sPaginationType": "full_numbers",
			"bPaginate": true,
			"bLengthChange": true,
	        	"bProcessing": true,
			"aaSorting": [[ 1, "asc" ]],
	        	"iDisplayLength": 5,
			"bDestroy": true,
			"bLengthChange": false,
	        	"sAjaxSource": "../ajax/aj_getDetailsProduct.php?id="+id_produto,
	        	"fnInitComplete": function(oSettings, json) {
		    	//correctDataTable();

		    }
	
			
    });
 	
		},
	close: function() {
			$(this).find("input[type=text]").val("");
			cleanErrorsOnForm($(this));
			//location.reload();
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
            <li><a href="encomendas.php">Encomendas</a></li><li><a href="pessoas.php">Pessoas</a></li>
<li><a href="novo_servico.php">Serviços</a></li>	    

        </ul>
   		</div>
    	</div>
 
        <div id="headerDrawer">
        </div>

    	<div id="pageContainer">
	<div id="dialogProduto-form" class="dialogForm" title="Detalhes do Produto" >
	
	<table cellpadding="0" cellspacing="0" border="0" class="display" id="detailProd" width="100%">
    	<thead>
        <tr>
            <th width="5%">#</th>
            <th align="left" width="40%">Consumidor</th>
            <th align="left" width="30%">Quantidade</th>

        </tr>
    	</thead>
    	<tfoot>
        <tr>
            <th>#</th>
            <th align="left">Consumidor</th>
            <th align="left">Quantidade</th>
           
        </tr>
    	</tfoot>
	</table>
	
	
	
	</div>
        <div class="navBar">
	<button id="returnProdutores" class="rtBut">PRODUTORES</button>
	<button id="detalhesProduto" class="detailsBut">Detalhes do Produto</button>
	</div>
    	 	<div style="width:220px;float:left; ">
    		<br/>
            <h3 style=" margin:0 10px;"><b>ENCOMENDA SEMANAL</b></h3><br/>
               <div  align="left" style="margin-left:10px ">
                <ul id="verticalmenu" class="glossymenu2">
                    <li><a href="cabaz_semanal_consumidores.php">CONSUMIDORES</a></li>
                    <li style="padding: 15px 0; padding-left: 10px";>PRODUTORES</li>
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
                 <h2>Encomenda semanal - <?php echo $name; ?></h2><hr>
             
                <table cellpadding="0" cellspacing="0" border="0" class="display" id="VerEncProdutores" width="100%">
   				 <thead>
        		 <tr>
                 	<th width="5%">#</th>
                    <th align="left" width="47%">Produto</th>
                    <th width="14%">Preço Unit.</th>
                    <th width="10%">Quantidade</th>
                    <th width="14%">Preço Total</th>
       		     </tr>
    			 </thead>
    			 <tfoot>
                 <tr>
                    <th>#</th>
                    <th align="left">Produto</th>
                    <th>Preço Unit.</th>
                    <th>Quantidade</th>
                    <th>Preço Total</th>
                    
        		</tr>
    			</tfoot>
			  </table>
                <br/>
                <?php echo "<p align='left'><b>Total da Encomenda: ".@number_format($cartTotal,2)." - Euros </b></p>"; ?>
          </div>
		</div>
      <?php require_once("../includes/core/footer.php"); ?>
</div>

</body></html>
