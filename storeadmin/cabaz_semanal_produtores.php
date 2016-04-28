<?php 
//Página protegida ao admin
ob_start();
session_start();

require_once("./inc/session_auth.php");
?>

<?php require_once("../includes/core/header.php"); ?> 
<link href="../style/css/include/demo_table_jui.css" rel="stylesheet" type="text/css"/>
<link href="../style/css/include/demo_page.css" rel="stylesheet" type="text/css"/>

<script>
$(function() {
	// build table
	$('#CabazProdutores').dataTable( {
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
			//"bInfo": false,
	        "sAjaxSource": "../ajax/aj_getCabazProdutores.php",
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
		var anSelected 	= fnGetSelected( "#CabazProdutores" );
    	if ( anSelected.length == 1 ) {
    		var id_produtor = anSelected.find("td").first().html();
	    	var name = anSelected.find("td").first().next().html();
			var total = anSelected.find("td").first().next().next().html();
			
			self.location='ver_encomenda_produtor.php?pid=' + id_produtor + '&n=' + name+'&t='+total;
		}
	});	
});
</script>

<div id="motherContainer">
  <div class="navBar">
        <button id="editPessoaBut" class="edBut">Ver Encomenda</button>
  </div>
  <?php require_once("../includes/site/nav.php"); ?>

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
        <?php require_once("inc/encomendas.nav.php"); ?>
      </div>
          <div style="width:780px;float:left;padding-left:15px">
          
          <br/><h2>Encomendas da semana - Produtores</h2><hr>
          <div align="left" >
       			<table cellpadding="0" cellspacing="0" border="0" class="display" id="CabazProdutores" width="100%">
   				 <thead>
        		 <tr>
                    <th width="5%">#</th>
                    <th align="left" width="65%">Produtor</th>
                    <th width="15%">Total</th>
                    
       		     </tr>
    			 </thead>
    			 <tfoot>
                 <tr>
                    <th>#</th>
                    <th align="left">Produtor</th>
                    <th>Total</th>
        		</tr>
    			</tfoot>
			  </table>
     
            </div>
          </div>
		</div>
      <?php require_once("../includes/core/footer.php"); ?>
</div>

</body></html>
