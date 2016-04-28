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
//Error Reporting
error_reporting(E_ALL);
ini_set('display_errors','1');
?>

<?php 
//Buscar lista de produtores
$produtor_list='';
	$sql=mysql_query("SELECT * FROM people  WHERE flag='cp' OR flag='acp' ORDER BY name ASC");
	
	$produtor_list.='<option value="">'."Escolher...".'</option>';
	
		while($row=mysql_fetch_array($sql)){
			$id_people=$row["id_people"];
			$produtorname=$row['name'];
		
			//Select Dinamico
			$produtor_list.='<option value="'.$id_people.'">'.$produtorname.'</option>';
		}
?>

<?php 
//Buscar lista de Categorias
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


<!-- /////////////////////////////////////////////////////////////////////////////////// -->
<script>

$(function() {
	// build table
	$('#produtos').dataTable( {
			"bJQueryUI": true,
	        "sPaginationType": "full_numbers",
			"fnDrawCallback": function() {
				////////////////////Details/////////////////
				var i=$('.imgPreviewBut1').length;
				$(this).find("tr").each(function(){
				var currentTD1 = $(this).find("td").first().next().next().next().next().next().next().next().next().next().next().next().next().next(),
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
							currentTD.html("<div class='imgPreviewBut ui-icon ui-icon-zoomin'></div><div class='imgPreview' style='width:75px'><img style='max-width:100%;' src='../p_images/"+imgsource+"'/>");
							}
						}
				 });
					$(".imgPreview").hide();	
			},
	        "bProcessing": true,
			"aaSorting": [[ 1, "asc" ]],
	        "iDisplayLength": 10,
	        "sAjaxSource": "../ajax/aj_getProdutos.php",
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
		$( "#dialogProduto-form" ).dialog( "option", "title", "Novo Produto" )
			.dialog( "open" )
			.find("#isEdit").val("false");

		$( "#dialogProduto-form" ).dialog().find(".currentImage").css("display","none");
		// console.log($( "#dialogProduto-form" ).dialog().find(".currentImage"));
	});

	$( ".delBut" ).button({
    	icons: {
            primary: "ui-icon-closethick"
        },
        text: true
    }).click(function() {
     	var anSelected 	= fnGetSelected( "#produtos" );
					if ( anSelected.length == 1 ) {
						var id_produto = anSelected.find("td").first().html();
						var product_name = anSelected.find("td").first().next().html();
						queryDialog("Tem a certeza que quer remover o Produto <b>"+product_name+"</b> do sistema?<br>",
							function(){ // fires if yes
								$.post("../ajax/aj_deleteProduto.php","id_produto="+id_produto,
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
         }
	});

	$( ".edBut" ).button({
    	icons: {
            primary: "ui-icon-wrench"
        },
        text: true
    }).click(function() {
		
		var anSelected 	= fnGetSelected( "#produtos" );
        if ( anSelected.length == 1 ) {
        	var pointer = anSelected.find("td").first();
	    	var dg 		= $( "#dialogProduto-form" );
			var id 		= pointer.html();
			var product_name 	= pointer.next().html();
			var price1 	= pointer.next().next().next().html();
			var unidade=  pointer.next().next().next().next().next().next().next().next().next().html();
			var price = price1.slice(0,-6);
			var id_produtor = pointer.next().next().next().next().html();
			var category  = pointer.next().next().next().next().next().html();
			var id_produtor =  pointer.next().next().next().next().next().next().next().next().next().next().next().html();
			var id_category =  pointer.next().next().next().next().next().next().next().next().next().next().next().next().html();
			var details =  pointer.next().next().next().next().next().next().next().next().next().next().next().next().next().html();
			//remover 'div' da string
			var div = document.createElement("div");
			div.innerHTML = details;

			var quantidade1 = pointer.next().next().next().next().html();
			if( unidade=="Sim"){
				var quantidade = quantidade1.slice(0,-6);
			}else{
				var quantidade = quantidade1.slice(0,-3);
			}
			
			var imagem 	= pointer.next().next().find("img").attr("src");
			console.log(imagem);
			
			pointer = pointer.next().next().next().next().next().next().next();
			var certBioChecked = (pointer.html()=="Biológico");
			var convBioChecked = (pointer.html()=="Conversão-Bio");
			var permaChecked = (pointer.html()=="Orgânico");
			var protintChecked = (pointer.html()=="Tradicional");
			
			pointer = pointer.next();
			var stockChecked = (pointer.html()=="Sim");
			pointer = pointer.next();
			var unitChecked = (pointer.html()=="Sim");
			pointer = pointer.next();
			var pesoChecked = (pointer.html()=="Sim");
			
			
			with (dg){
			
		    	dialog( "option", "title", "Editar Produto - #"+id );
		    	find("#isEdit").val(id);
		    	find("#product_name").val(product_name);
				find("#price").val(price);
				
				find("#id_category").val(id_category);	
				find("#id_produtor").val(id_produtor);
				find("#quantidade").val(quantidade);
				find("#details").val(div.innerText);
				find("#stock").prop('checked',stockChecked);
				find("#unit").prop('checked',unitChecked);
				find("#peso").prop('checked',pesoChecked);
				
				find("#certBio").prop('checked',certBioChecked);
				find("#convBio").prop('checked',convBioChecked);
		    	find("#perma").prop('checked',permaChecked);
		    	find("#protint").prop('checked',protintChecked);
				
				
		    	find(".currentImage").css("display","block").find("img").attr("src",imagem);
					
				dialog( "open" );
				
				
			}
        }
	});
	
	// dialog nova pessoa
	$( "#dialogProduto-form" ).dialog({
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
					$.post("../ajax/aj_manageProduto.php",$(this).find("form").serialize(),
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
			$("#dialogProduto-form").parent().find(".ui-dialog-buttonset button").first().attr('disabled','true'); // disable the proceed button while uploadin
		},
		onComplete:function(fileName){
			$("#dialogProduto-form").find("#fileName").val(fileName);
			$("#dialogProduto-form").parent().find(".ui-dialog-buttonset button").first().removeAttr('disabled');
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
        <div id="dialogProduto-form" class="dialogForm" title="Nova Pessoa">
	<form>
	<fieldset>
    	<div>
			<label for="stock">Stock</label>
            
				<input type="checkbox" id="stock" name="stock" title="Produto em stock." />
			
		</div>
		<div>
			<label for="nome">Nome Produto</label><br/>
			<input type="text" name="product_name" id="product_name" size="35px" title="O mais curto possível!" class="text ui-widget-content ui-corner-all defaultText" />
		</div>
        <div>
			<label for="funcoes">Classificação</label>
			<div class="checkboxFormat">
				
                <input type="checkbox" id="certBio" name="b"/><label for="certBio" class="inline">Certificação Biológica</label><br>
                <input type="checkbox" id="convBio" name="c"/><label for="convBio" class="inline">Converção Biológico</label><br>
				<input type="checkbox" id="perma" name="p"/><label for="perma" class="inline">Produto Orgânico</label><br>
				<input type="checkbox" id="protint" name="prot"/><label for="protint" class="inline">Protecção Integrada ou Tradicional
</label>
			</div>
		<div>
			<label for="email">Preço</label><br/>
			<input type="text" name="price" id="price" title="Exemplo: 1.00" class="err_number text ui-widget-content ui-corner-all" />€/Kg ou €/Unid.
		</div>
        <div>
			<label for="unit">Venda por Unidade</label>
				<input type="checkbox" id="unit" name="unit" title="Ex(400g de Tofu, Frasco de Compota, etc ...)"/>
			
		</div>
        <div>
			<label for="peso">Venda por Unidade, mas necessário aferir o peso. Ex( Alface, abobora,...)</label>
				<input type="checkbox" id="peso" name="peso" title="Produto vendido à unidade, mas necessário aferir o peso.
Ex( Alface, abobora, mólho de Salsa, mólho de Manjericão, etc ...)"/>
			
		</div>
       
        <div>
        	<label for="category">Categoria</label><br/>
       	 	<select name="id_category" id="id_category" class="err_selected text ui-widget-content ui-corner-all defaultText">
         	<?php echo $category_list; ?>
        	</select>
        </div>       
		<div>
			<label for="produtor">Produtor</label><br/>
        	<select name="id_produtor" id="id_produtor" class="err_selected_prod text ui-widget-content ui-corner-all">
			<?php echo $produtor_list; ?>
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
		
        
            
		<input type="hidden" id="isEdit" name="isEdit" value="false"/>
	</fieldset>
	</form>
    </div>
      <div class="navBar">
	<button id="novoProdutoBut" class="nvBut">Novo Produto</button>
	<button id="editProdutoBut" class="edBut">Modificar</button>
	<button id="deleteProdutoBut" class="delBut">Remover</button>
</div>
        <h2>Lista de Produtos</h2><hr/>       		
				<table cellpadding="0" cellspacing="0" border="0" class="display" id="produtos" width="100%">
    <thead>
        <tr>
            <th width="1%">#</th>
            <th width="40%">Produto</th>
            <th width="10%">Imagem</th>
            <th width="30%">Preço</th>
            <th width="20%">Quant.</th>
            <th width="20%">Produtor</th>
            <th width="5%">Categoria</th>
            <th width="10%">cultura</th>
            <th width="1%">Stock</th>
            <th width="1%">Unid.</th>
            <th width="1%">Peso</th>
            <th width="1%">#</th>
            <th width="1%">#</th>
            <th width="5%">Detalhes</th>
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Id</th>
            <th>Produto</th>
            <th>Imagem</th>
            <th>Preço</th>
            <th>Quant.</th>
            <th>Produtor</th>
            <th>Categoria</th>
            <th>Cultura</th>
            <th>Stock</th>
            <th>Unid.</th>
            <th>Peso</th>
            <th>#</th>
            <th>#</th>
            <th>Detalhes</th>
        </tr>
    </tfoot>
</table>
      
    	<br />
    	<br />
        </div>
      		<?php require_once("../includes/core/footer.php"); ?>
</div>
</body></html>
