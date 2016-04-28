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
	        "sAjaxSource": "../ajax/aj_getEditarCabazConsumidores.php?id="+x,
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
		
		self.location='cabaz_semanal_consumidores.php';
	});

	$( ".delBut" ).button({
    	icons: {
            primary: "ui-icon-closethick"
        },
        text: true
    }).click(function() {
  		var anSelected 	= fnGetSelected( "#EditarCabazConsumidores" );
    	if ( anSelected.length == 1 ) {
    		var id_produto = anSelected.find("td").first().html();
	    	var produto = anSelected.find("td").first().next().html();
			var id_encomenda = x;
			// Initialize your table
    		var oTable = $('#EditarCabazConsumidores').dataTable();
    		// Get the length
   			 var count = oTable.fnGetData().length;
			if(count==1){
			queryDialog("Tem a certeza que quer remover o <b>último</b> Produto <b>"+produto+"</b> da Encomenda?<br><br> A encomenda será eliminada do sistema!",
				function(){ // fires if yes
					$.post("../ajax/aj_deleteEditarCabazConsumidores.php","id_produto="+id_produto+"&id_encomenda="+id_encomenda,
						function(data) {
							console.log("data="+data+"=");
							if(data.substr(data,1)=="{"){
								anSelected.remove();
								location.reload();
							}else{
								popupDialog("<b>A operação não foi bem sucedida</b><br><br>Occorreu um erro de comunicação ou sintaxe para com a base de dados. Contacte o administrador.","error");
							}
						}
					);
				}
			);
			}else{
			queryDialog("Tem a certeza que quer remover o Produto <b>"+produto+"</b> da Encomenda?<br>",
				function(){ // fires if yes
					$.post("../ajax/aj_deleteEditarCabazConsumidores.php","id_produto="+id_produto+"&id_encomenda="+id_encomenda,
						function(data) {
							console.log("data="+data+"=");
							if(data.substr(data,1)=="{"){
								anSelected.remove();
								location.reload();
							}else{
								popupDialog("<b>A operação não foi bem sucedida</b><br><br>Occorreu um erro de comunicação ou sintaxe para com a base de dados. Contacte o administrador.","error");
							}
						}
					);
				}
			);}
			
        }
	});

	$( ".edBut" ).button({
    	icons: {
            primary: "ui-icon-wrench"
        },
        text: true
    }).click(function() {
		
		var anSelected 	= fnGetSelected( "#EditarCabazConsumidores" );
        if ( anSelected.length == 1 ) {
        	var pointer = anSelected.find("td").first();
	    	var dg 		= $( "#dialogEditarEncConsumidor-form" );
			var id 		= pointer.html();
			var produto = pointer.next().html();
			var quant 	= pointer.next().next().next().next().html();
			var res = quant.slice(0,4);
			
			with (dg){
		    	dialog( "option", "title", "Editar - #"+produto );
		    	find("#isEdit").val(id);
		    	find("#quant").val(res);
				dialog( "open" );	
			}
        }
	});
	
	// dialog nova pessoa
	$( "#dialogEditarEncConsumidor-form" ).dialog({
		autoOpen: false,
		resizable: false,
		modal: true,
		open: function(event, ui){

		},
		buttons: {
			"Guardar": function() {
				cleanErrorsOnForm($(this));
				var bValid = validateForm($(this));
				if(bValid){
					console.log($(this).find("form").serialize());
					$.post("../ajax/aj_manageEditarCabazConsumidores.php?",$(this).find("form").serialize(),
						function(data) {
							console.log("data="+data+"=");
							if(data.substr(data,1)=="{"){
								location.reload();
							}else{
								if(data=="1062")	// duplicate error
									popupDialog("O produto que inseriu já existe na base de dados.<br><br>Os produtos não podem ter nomes iguais. Use uma designação extra para o produto. Por exemplo 'Batata Nova', 'Batata Normal'","error");
								else if(data=="1205")	// duplicate error
									popupDialog("A operação excedeu o tempo limite.<br><br>Isto pode acontecer pela internet estar com problemas ou o servidor estar em carga.","error");
								else				// other error
									popupDialog("<b>A operação não foi bem sucedida</b><br><br>Occorreu um erro de comunicação ou sintaxe para com a base de dados. Contacte o administrador.","error");
							}
						}
					);
				}

			},
			"Cancelar": function() {
				$( this ).dialog( "close" );
			}
		},
		close: function() {
			$(this).find("input[type=text]").val("");
			cleanErrorsOnForm($(this));
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
	<button id="novoProdutoBut" class="nvBut">Novo Produto</button>
	<button id="editProdutoBut" class="edBut">Editar Produto</button>
	<button id="deleteProdutoBut" class="delBut">Remover Produto</button>
	<button id="returnConsumidores" class="rtBut">CONSUMIDORES</button>
	</div>
<div id="dialogEditarEncConsumidor-form" class="dialogForm" title="Nova Categoria">
        <form>
        <fieldset>
		<div>
			<label for="quant">Quantidade</label>
			<input type="text" name="quant" id="quant" size="15" title="Exemplo - 1.50" /> Kg/Unid.
		</div>
		<input type="hidden" id="isEdit" name="isEdit" value="false"/>
        <input type="hidden" id="id_encomenda" name="id_encomenda" value="<?php echo $targetID; ?>"/>
        </fieldset>
        </form>
	</div>
    			
    <?php require_once("inc/encomendas.nav.php"); ?>
          </div>
          <div style="width:780px;float:left;padding-left:15px;">
    		<br />
            <h2>Encomenda semanal - <?php echo $nomeCliente; ?></h2><hr>
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
