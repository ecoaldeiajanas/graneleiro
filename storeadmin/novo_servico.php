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
<link href="../style/css/include/fileuploader.css" rel="stylesheet" type="text/css"/>

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
	$('#servicos').dataTable( {
			"bFilter": false,
			"fnDrawCallback": function() {
				////////////////////Details/////////////////
				var i=$('.imgPreviewBut1').length;
				$(this).find("tr").each(function(){
				var currentTD1 = $(this).find("td").first().next().next().next().next().next().next(),
					   obs = currentTD1.html();
						if(obs){
							if(!i){
								currentTD1.html("<div class='imgPreviewBut1 ui-icon ui-icon-zoomin'></div><div class='obs'>"+obs+"");
								  }
						}
				 });
	       			$(".obs").hide();

				////////////////////Imagem/////////////////
				var i=$('.imgPreviewBut').length;
				$(this).find("tr").each(function(){
				var currentTD = $(this).find("td").first().next().next(),
					  imgsource = currentTD.html();
						if(imgsource){
							if(!i){
							currentTD.html("<div class='imgPreviewBut ui-icon ui-icon-zoomin'></div><div class='imgPreview' style='width:75px'><img style='max-width:100%;' src='../p_images/"+imgsource+"'/>");
							}
						}
				 });
					$(".imgPreview").hide();	
			},
			"bJQueryUI": true,
	        "sPaginationType": "full_numbers",
			"bPaginate": true,
			"bLengthChange": true,
	        "bProcessing": true,
	        "iDisplayLength": 10,
			
	        "sAjaxSource": "../ajax/aj_getServicos.php",
	        "fnInitComplete": function(oSettings, json) {
			////////////////////Details/////////////////
	        	$(".imgPreviewBut1").live('click',function(){
	        		if($(this).next().is(":visible")){
	        			$(this).next().hide('fast');
	        			$(this).removeClass("ui-icon-zoomout").addClass("ui-icon-zoomin");
	        		}else{
	        			$(this).next().show('fast'); 
	        			$(this).removeClass("ui-icon-zoomin").addClass("ui-icon-zoomout");
	        		}
				
				});
				////////////////////Imagem/////////////////
	        	$(".imgPreviewBut").live('click',function(){
	        		if($(this).next().is(":visible")){
	        			$(this).next().hide('fast');
	        			$(this).removeClass("ui-icon-zoomout").addClass("ui-icon-zoomin");
	        		}else{
	        			$(this).next().show('fast');
	        			$(this).removeClass("ui-icon-zoomin").addClass("ui-icon-zoomout");
	        		}
	        	});
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
		$( "#dialogServico-form" ).dialog( "option", "title", "Novo Serviço" )
			.dialog( "open" )
			.find("#isEdit").val("false");

		$( "#dialogServico-form" ).dialog().find(".currentImage").css("display","none");
		console.log($( "#dialogServico-form" ).dialog().find(".currentImage"));
	});

	$( ".delBut" ).button({
    	icons: {
            primary: "ui-icon-closethick"
        },
        text: true
    }).click(function() {
    	var anSelected 	= fnGetSelected( "#servicos" );
    	if ( anSelected.length == 1 ) {
    		var id_servico = anSelected.find("td").first().html();
	    	var servico = anSelected.find("td").first().next().html();
			queryDialog("Tem a certeza que quer remover o serviço <b>"+servico+"</b> do sistema?<br>",
				function(){ // fires if yes
					$.post("../ajax/aj_deleteServico.php","id_servico="+id_servico,
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
		
		var anSelected 	= fnGetSelected( "#servicos" );
        if ( anSelected.length == 1 ) {
        	var pointer = anSelected.find("td").first();
	    	var dg 		= $( "#dialogServico-form" );
			var id_servico 		= pointer.html();
			var servico 	= pointer.next().html();
			var imagem 	= pointer.next().next().html();
			var nome 	= pointer.next().next().next().html();	
			var telef 	= pointer.next().next().next().next().html();
			var email 	= pointer.next().next().next().next().next().html();	
			var obs 	= pointer.next().next().next().next().next().next().html();	
			//remover 'div' da string
			var div = document.createElement("div");
			div.innerHTML = obs;	

			var imagem 	= pointer.next().next().find("img").attr("src");
			console.log(imagem);								
			with (dg){
			
		    	dialog( "option", "title", "Editar Serviço - #"+id_servico );
		    	find("#isEdit").val(id_servico);
		    	find("#servico").val(servico);
		    	find("#nome").val(nome);
		    	find("#telef").val(telef);
		    	find("#email").val(email);
		    	find("#obs").val(div.innerText);
			find(".currentImage").css("display","block").find("img").attr("src",imagem);
				dialog( "open" );			
			}
        }
	});
	
	// dialog nova pessoa
	$( "#dialogServico-form" ).dialog({
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
					$.post("../ajax/aj_manageServico.php",$(this).find("form").serialize(),
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
				$(':input','#dialogServico-form')
 				//.not(':button, :submit, :reset, :hidden')
 				.val('')
 				.removeAttr('checked')
 				.removeAttr('selected');
				$( this ).dialog( "close" );
				
			}
		},
		close: function() {
			$(this).find("input[type=text]").val("");
			cleanErrorsOnForm($(this));
		}
	});

	$("#file-uploader").SimpleFileUploader({
		onBeforeStart:function(){
			$("#dialogServico-form").parent().find(".ui-dialog-buttonset button").first().attr('disabled','true'); // disable the proceed button while uploadin
		},
		onComplete:function(fileName){
			$("#dialogServico-form").find("#fileName").val(fileName);
			$("#dialogServico-form").parent().find(".ui-dialog-buttonset button").first().removeAttr('disabled');
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
            <button id="novoProdutoBut" class="nvBut">Novo Serviço</button>
            <button id="editPessoaBut" class="edBut">Editar</button>
            <button id="deletePessoaBut" class="delBut">Remover</button>
		</div>
	<div id="dialogServico-form" class="dialogForm" title="Nova Categoria">
        <form>
        <fieldset>
		<div>
			<label for="servico">Serviço</label><br/>
			<input type="text" name="servico" id="servico" size="35px" class="err_mandatory text ui-widget-content ui-corner-all defaultText"  />
		</div>
		<div>
			<label for="nome">Nome</label><br/>
			<input type="text" name="nome" id="nome" size="35px" class="err_mandatory text ui-widget-content ui-corner-all defaultText" />
		</div>
		<div>
			<label for="telf">Telefone</label><br/>
			<input type="text" name="telef" id="telef" class="err_number text ui-widget-content ui-corner-all"/>
		</div>
		<div>
			<label for="email">E-Mail</label><br/>
			<input type="text" name="email" size="35px" id="email" title="abc@dominio.com" class="err_mandatory err_email text ui-widget-content ui-corner-all defaultText"/>
		</div>
		<div>
			<label for="obs">Descrição</label><br/>
			<textarea id="obs" name="obs" cols="35" rows="5" class="text ui-widget-content ui-corner-all" ></textarea>
		</div>
		<div>
			<label for="imagem">Imagem</label>
			<div class="currentImage" style="display:hidden; width:200px; font-size: 0.7em;">
				<p>A imagem actual é esta:</p>
				<img style='max-width:100%' src=''/>
				<p>Mas pode substituir com outra:</p>
			</div>
			<input type="hidden" id="fileName" name="fileName" value=""/>
			<div id="file-uploader">
			    <noscript>
			        <p>Please enable JavaScript to use file uploader.</p>
			        <!-- or put a simple form for upload here -->
			    </noscript>
			</div>
		</div>     
		<input type="hidden" id="isEdit" name="isEdit" value="false"/>
        </fieldset>
        </form>
	</div>
        <h2>Serviços</h2><hr>
          <div align="left" style="margin-left:24px; margin-right:24px; ">
          
          		<table cellpadding="0" cellspacing="0" border="0" class="display" id="servicos" width="100%">
   				 <thead>
        		 <tr>
                    <th width="5%">#</th>
                    <th width="20%">Serviço</th>
		    <th width="10%">Imagem</th>
		    <th width="20%">Nome</th>
		    <th width="10%">Telefone</th>
		    <th width="15%">E-mail</th>
		    <th width="35%">Descrição</th>
       		     </tr>
    			 </thead>
    			 <tfoot>
                 <tr>
                    <th>#</th>
                    <th>Serviço</th>
                    <th>Imagem</th>
                    <th>Nome</th>
                    <th>Telefone</th>
                    <th>E-mail</th>
                    <th>Descrição</th>
        		</tr>
    			</tfoot>
			  </table>
      </div>
      
  </div>
        
      <?php require_once("../includes/core/footer.php"); ?>
    </div>
</body></html>
