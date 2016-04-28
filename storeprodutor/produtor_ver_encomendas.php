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
//Verificar se existe encomenda da semana
$id_people=$_SESSION["id"];
	 $sql=mysql_query("SELECT id_people, semana FROM encomenda WHERE id_people=$id_people AND semana=0  LIMIT 1");
		$existEnc=mysql_num_rows($sql);
?>

<?php
//Id produtor
$targetID=$_SESSION['id'];
?>
  
<?php require_once("../includes/core/header.php"); ?> 
<link href="../style/css/include/demo_table_jui.css" rel="stylesheet" type="text/css"/>
<link href="../style/css/include/demo_page.css" rel="stylesheet" type="text/css"/>

<!--///////////////////////////////////////////////////////////////////////////////-->
<script>
$(function() {
var x = <?php echo $targetID; ?>;
	// build table
	$('#ProdutorEnc').dataTable( {
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
			//"bInfo": false,
	        "sAjaxSource": "../ajax/aj_getProdutorEncomenda.php?id="+x,
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
                iTotalMarket += aaData[i][4]*1;
            }
			document.getElementById("total").innerHTML='<b>Total da Encomenda: '+ iTotalMarket.toFixed(2) +' - Euros </b>';
        }
	
		});

	$( ".detailsBut" ).button({
    	icons: {
            primary: "ui-icon-extlink"
        },
        text: true
    	}).click(function() {
		var anSelected 	= fnGetSelected( "#ProdutorEnc" );
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
        	<li><a href="produtor_listar_produtos.php">Produtos</a></li> - 
            <li><a href="produtor_ver_encomendas.php">Encomenda da Semana</a></li>
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
	<button id="detalhesProduto" class="detailsBut">Detalhes do Produto</button>
	</div>

	<h2>Encomenda da Semana - <?php echo $_SESSION['name']; ?></h2><hr>

                <table cellpadding="0" cellspacing="0" border="0" class="display" id="ProdutorEnc" width="100%">
   				 <thead>
        		 <tr>
                 	<th width="5%">#</th>
                    <th width="61%">Produto</th>
                    <th width="12%">Preço Unit.</th>
                    <th width="10%">Quantidade</th>
                    <th width="12%">Preço Total</th>
       		     </tr>
    			 </thead>
    			 <tfoot>
                 <tr>
                    <th>#</th>
                    <th>Produto</th>
                    <th>Preço Unit.</th>
                    <th>Quantidade</th>
                    <th>Preço Total</th>
        		</tr>
    			</tfoot>
			  </table> 
                <br/>
                <p id="total"> </p>

      </div>
      <?php require_once("../includes/core/footer.php"); ?>
    </div>

</body></html>
