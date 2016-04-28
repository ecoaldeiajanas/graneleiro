<?php 
//Pagina protegida ao ADMIN
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
//Error Reporting
error_reporting(E_ALL);
ini_set('display_errors','1');
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

<?php require_once("../includes/core/header.php"); require_once("../includes/dialogs.php");?> 
<link href="../style/css/include/demo_table_jui.css" rel="stylesheet" type="text/css"/>
<link href="../style/css/include/demo_page.css" rel="stylesheet" type="text/css"/>

<!-- /////////////////////////////////////////////////////////////////////////////////// -->
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

	// build table
	$('#categorias').dataTable( {
			"bFilter": false,
			"bJQueryUI": true,
	        "sPaginationType": "full_numbers",
			"bPaginate": true,
			"bLengthChange": true,
	        "bProcessing": true,
	        "iDisplayLength": 10,
			
	        "sAjaxSource": "../ajax/aj_getCategorias.php",
	        "fnInitComplete": function(oSettings, json) {
		    	correctDataTable();
		    }
			
    });
	// build the top bar on the table
    $( ".nvBut" ).button({
    	icons: {
            primary: "ui-icon-circle-plus"
        },
        text: true
    }).click(function() {
		$( "#dialogCategory-form" ).dialog( "option", "title", "Novo Produto" )
			.dialog( "open" )
			.find("#isEdit").val("false");

		$( "#dialogCategory-form" ).dialog().find(".currentImage").css("display","none");
		console.log($( "#dialogProduto-form" ).dialog().find(".currentImage"));
	});

	$( ".delBut" ).button({
    	icons: {
            primary: "ui-icon-closethick"
        },
        text: true
    }).click(function() {
    	var anSelected 	= fnGetSelected( "#categorias" );
    	if ( anSelected.length == 1 ) {
    		var id_category = anSelected.find("td").first().html();
	    	var category = anSelected.find("td").first().next().html();
			queryDialog("Tem a certeza que quer remover a categoria <b>"+category+"</b> do sistema?<br>",
				function(){ // fires if yes
					$.post("../ajax/aj_deleteCategoria.php","id_category="+id_category,
						function(data) {
							console.log("data="+data+"=");
							if(data.substr(data,1)=="{"){
								anSelected.remove();
							}else{
								popupDialog("<b>A operação não foi bem sucedida</b><br><br>Occorreu um erro de comunicação ou sintaxe para com a base de dados. Contacte o administrador.","error");
							}
						}
					);
				}
			);
        }
	});

$( ".edBut" ).button({
    	icons: {
            primary: "ui-icon-wrench"
        },
        text: true
    }).click(function() {
		
		var anSelected 	= fnGetSelected( "#categorias" );
        if ( anSelected.length == 1 ) {
        	var pointer = anSelected.find("td").first();
	    	var dg 		= $( "#dialogCategory-form" );
			var id 		= pointer.html();
			var category 	= pointer.next().html();
			
			with (dg){
			
		    	dialog( "option", "title", "Editar Categoria - #"+id );
		    	find("#isEdit").val(id);
		    	find("#category").val(category);
				dialog( "open" );			
			}
        }
	});
	
	// dialog nova pessoa
	$( "#dialogCategory-form" ).dialog({
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
					$.post("../ajax/aj_manageCategoria.php",$(this).find("form").serialize(),
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
<!-- /////////////////////////////////////////////////////////////////////////////////// --> 
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
            <button id="novoProdutoBut" class="nvBut">Nova Categoria</button>
            <button id="editPessoaBut" class="edBut">Editar</button>
            <button id="deletePessoaBut" class="delBut">Remover</button>
		</div>
	<div id="dialogCategory-form" class="dialogForm" title="Nova Categoria">
        <form>
        <fieldset>
		<div>
			<label for="category">Categoria</label>
			<input type="text" name="category" id="category" />
		</div>
		<input type="hidden" id="isEdit" name="isEdit" value="false"/>
        </fieldset>
        </form>
	</div>
        <h2>Categorias</h2><hr>
          <div align="left" style="margin-left:24px; margin-right:24px; ">
          
          		<table cellpadding="0" cellspacing="0" border="0" class="display" id="categorias" width="100%">
   				 <thead>
        		 <tr>
                    <th width="5%">#</th>
                    <th width="95%">Categoria</th>
       		     </tr>
    			 </thead>
    			 <tfoot>
                 <tr>
                    <th>#</th>
                    <th>Categoria</th>
        		</tr>
    			</tfoot>
			  </table>
      </div>
      
  </div>
        
      <?php require_once("../includes/core/footer.php"); ?>
    </div>
</body></html>
