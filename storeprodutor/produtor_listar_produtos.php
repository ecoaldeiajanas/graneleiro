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
//lista de Categorias
//id_produtor
$targetID=$_SESSION['id'];
$category_list='';
	$sql=mysql_query("SELECT * FROM category ORDER BY category ASC");
	$category_list.='<option value="">'."Escolher...".'</option>';
		while($row=mysql_fetch_array($sql)){
			$id_category=$row["id_category"];
			$category=$row['category'];
			//Select Dinamico
			$category_list.='<option value="'.$id_category.'">'.$category.'</option>';
		}
?>
  
<?php require_once("../includes/core/header.php"); require_once("../includes/dialogs.php");?> 
<link href="../style/css/include/demo_table_jui.css" rel="stylesheet" type="text/css"/>
<link href="../style/css/include/demo_page.css" rel="stylesheet" type="text/css"/>
<link href="../style/css/include/fileuploader.css" rel="stylesheet" type="text/css"/>
<script type="text/javascript" src="../js/s2/simpleFileUploader.js"></script>

<script>
$(function() {
var x = '<?php echo $targetID ?>';
	// build table
	$('#ListarProdProdutor').dataTable( {
			//"bServerSide": true,
			"oLanguage": {
         	"sLoadingRecords": "Não existem Produtos."
      		 },
			"bJQueryUI": true,
	        "sPaginationType": "full_numbers",
			"fnDrawCallback": function() {
				////////////////////Details/////////////////
				var i=$('.imgPreviewBut1').length;
				$(this).find("tr").each(function(){
				var currentTD1 = $(this).find("td").first().next().next().next().next().next().next().next().next().next(),
					   details = currentTD1.html();
						if(details){
							if(!i){
								currentTD1.html("<div class='imgPreviewBut1 ui-icon ui-icon-zoomin'></div><div class='details'>"+details+"");
								  }
						}
				 });
	       			$(".details").hide();
				////////////////////Imagem/////////////////
				var i=$('.imgPreviewBut').length;
				$(this).find("tr").each(function(){
				var currentTD = $(this).find("td").first().next().next(),
					  imgsource = currentTD.html();
						if(imgsource){
							if(!i){
							currentTD.html("<div class='imgPreviewBut ui-icon ui-icon-zoomin'></div><div class='imgPreview' style='width:120px'><img style='max-width:100%;' src='../p_images/"+imgsource+"'/>");
							}
						}
				 });
					$(".imgPreview").hide();	
			},
			
			"bPaginate": true,
			"bLengthChange": true,
	        //"bProcessing": true,
	        "iDisplayLength": 10,
	        "sAjaxSource": "../ajax/aj_getProdutosProdutor.php?id="+x,
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


	// build the top bar on the table
    $( ".nvBut" ).button({
    	icons: {
            primary: "ui-icon-circle-plus"
        },
        text: true
    }).click(function() {
		$( "#dialogProdutoProdutor-form" ).dialog( "option", "title", "Novo Produto" )
			.dialog( "open" )
			.find("#isEdit").val("false");

		$( "#dialogProdutoProdutor-form" ).dialog().find(".currentImage").css("display","none");
		// console.log($( "#dialogProduto-form" ).dialog().find(".currentImage"));
	}).makeEditable;

	$( ".delBut" ).button({
    	icons: {
            primary: "ui-icon-closethick"
        },
        text: true
    }).click(function() {
			var anSelected 	= fnGetSelected( "#ListarProdProdutor" );
					if ( anSelected.length == 1 ) {
						var id_produto = anSelected.find("td").first().html();
						var product_name = anSelected.find("td").first().next().html();
						queryDialog("Tem a certeza que quer remover o Produto <b>"+product_name+"</b> do sistema?<br>",
							function(){ // fires if yes
								$.post("../ajax/aj_deleteProdutoProdutor.php","id_produto="+id_produto,
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
		
		var anSelected 	= fnGetSelected( "#ListarProdProdutor" );
        if ( anSelected.length == 1 ) {
        	var pointer = anSelected.find("td").first();
	    	var dg 		= $( "#dialogProdutoProdutor-form" );
			var id 		= pointer.html();
			var product_name 	= pointer.next().html();
			var imagem 	= pointer.next().next().find("img").attr("src");
			console.log(imagem);
			var price1 = pointer.next().next().next().html();
			var price = price1.slice(0,-6);
			var id_category = pointer.next().next().next().next().next().next().next().next().html();
			var quantidade1 = pointer.next().next().next().next().next().html();
			var unidade =  pointer.next().next().next().next().next().next().html();
			if( unidade=="Sim"){
				var quantidade = quantidade1.slice(0,-6);
			}else{
				var quantidade = quantidade1.slice(0,-3);
			}
			var details1= pointer.next().next().next().next().next().next().next().next().next().html();
			//remover 'div' da string
			var div = document.createElement("div");
			div.innerHTML = details1;
			
			pointer = pointer.next().next().next().next().next().next();
			var unitChecked = (pointer.html()=="Sim");
			pointer = pointer.next();
			var pesoChecked = (pointer.html()=="Sim");
		
			with (dg){
		    	dialog( "option", "title", "Editar Produto - #"+id );
		    	find("#isEdit").val(id);
		    	find("#product_name").val(product_name);
		    	
				find("#price").val(price);
				find("#id_category").val(id_category);
				//find("#id_category").selectBox('destroy').val(id_category).selectBox();
				find("#quantidade").val(quantidade);
				find("#details").val(div.innerText);
				
				find("#unit").prop('checked',unitChecked);
				find("#peso").prop('checked',pesoChecked);
				find(".currentImage").css("display","block").find("img").attr("src",imagem);
				dialog( "open" );
			}
        }
	});
	
	// dialog nova pessoa
	$( "#dialogProdutoProdutor-form" ).dialog({
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
					//console.log
					($(this).find("form").serialize());
					$.post("../ajax/aj_manageProdutoProdutor.php",$(this).find("form").serialize(),
						function(data) {
							console.log("data="+data+"=");
							if(data.substr(data,1)=="{"){
								location.reload();
							}else if(data=="1205"){	// duplicate error
									popupDialog("A operação excedeu o tempo limite.<br><br>Isto pode acontecer pela internet estar com problemas ou o servidor estar em carga.","error");
							}else{				// other error
									popupDialog("<b>A operação não foi bem sucedida</b><br><br>Occorreu um erro de comunicação ou sintaxe para com a base de dados. Contacte o administrador.","error");
							}
						}
					);
				}

			},
			"Cancelar": function() {
				$(':input','#dialogProduto-form')
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
			$("#dialogProdutoProdutor-form").parent().find(".ui-dialog-buttonset button").first().attr('disabled','true'); // disable the proceed button while uploadin
		},
		onComplete:function(fileName){
			$("#dialogProdutoProdutor-form").find("#fileName").val(fileName);
			$("#dialogProdutoProdutor-form").parent().find(".ui-dialog-buttonset button").first().removeAttr('disabled');
		}
	});
});
</script>
<div id="dialogProdutoProdutor-form" class="dialogForm" title="Nova Pessoa">
	<form>
	<fieldset>
		<input type="hidden" id="isEdit" name="isEdit" value=""/>
		<div>
			<label for="product_name">Produto</label>
			<input type="text" name="product_name" id="product_name" title="Nome do Produto" class="err_name text ui-widget-content ui-corner-all defaultText" />
		</div>
        <div>
			<label for="price">Preço</label>
			<input type="text" name="price" id="price" title="Exemplo: 1.50" class="err_number text ui-widget-content ui-corner-all defaultText" />
		</div>
        <div>
			<label for="unit">Venda por Unidade</label>
			<input type="checkbox" id="unit" name="unit" title="Ex(400g de Tofu, Frasco de Compota, etc ...)"/>
		</div>     
        <div>
			<label for="peso">Produto vendido à unidade, mas necessário aferir o peso.<br/> Ex( Alface, Abobora, ...)" </label>
				<input type="checkbox" id="peso" name="peso" title="Produto vendido à unidade, mas necessário aferir o peso.
Ex( Alface, mólho de Salsa, mólho de Manjericão, etc ...)"/>
		</div>
         <div>
        	<label for="category">Categoria</label><br/>
       	 	<select name="id_category" id="id_category" class="err_selected text ui-widget-content ui-corner-all defaultText">
         	<?php echo $category_list; ?>
        	</select>
        </div> 
        <div>
			<label for="quantidade">Quantidade</label><br/>
			<input type="text" name="quantidade" id="quantidade" class="err_number text ui-widget-content ui-corner-all" />Kg / Unid.
		</div>
         
        <div>
			<label for="details">Detalhes</label>
			<textarea id="details" name="details" cols="35" rows="5" class="text ui-widget-content ui-corner-all" ></textarea>
		</div>
        <input type="hidden" id="isEdit" name="isEdit" value="false"/>
        <input type="hidden" id="id_produtor" name="id_produtor" value="<?php echo $targetID; ?>"/>
		<div>
			<label for="image">Imagem</label>
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
	</fieldset>
	</form>
</div>
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
    <div class="navBar">
	<button id="novoProdutoBut" class="nvBut">Novo Produto</button>
	<button id="editProdutoBut" class="edBut">Editar Produto</button>
	<!--<button id="deleteProdutoBut" class="delBut">Remover Produto</button>-->
	</div>
	<h2>Produtos - <?php echo $_SESSION['name']; ?></h2><hr>
            
          <table cellpadding="0" cellspacing="0" border="0" class="display" id="ListarProdProdutor" width="100%">
   				 <thead>
        		 <tr>
                    <th width="5%">#</th>
                    <th width="20%">Produto</th>
                    <th width="15%">Imagem</th>
                    <th width="10%">Preço</th>
                    <th width="10%">Categoria</th>
                    <th width="10%">Quantidade</th>
                    <th width="5%">Unid.</th>
                    <th width="5%">Peso</th>
                    <th width="1%">#</th>
                    <th width="25%">Detalhes</th>
		    <th width="5%">Stock</th>
       		     </tr>
    			 </thead>
    			 <tfoot>
                 <tr>
                    <th>#</th>
                    <th>Produto</th>
                    <th>Imagem</th>
                    <th>Preço</th>
                    <th>Categoria</th>
                    <th>Quantidade</th>                    
		    <th>Unid.</th>
                    <th>Peso</th>
                    <th>#</th>
                    <th>Detalhes</th>
		    <th>Stock</th>
        		</tr>
    			</tfoot>
			  </table>
      </div>
      <?php require_once("../includes/core/footer.php"); ?>
    </div>

</body></html>
