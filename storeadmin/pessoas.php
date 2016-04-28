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
<script>
$(function() {

	// build table
	$('#produtores').dataTable( {
			"bJQueryUI": true,
	        "sPaginationType": "full_numbers",
			"bPaginate": true,
			"bLengthChange": true,
	        "bProcessing": true,
			"aaSorting": [[ 1, "asc" ]],
	        "iDisplayLength": 10,
			
	        "sAjaxSource": "../ajax/aj_getPessoas.php",
	        "fnInitComplete": function(oSettings, json) {
		    	correctDataTable();
		    }
			
    });
	// build the top bar on the table
    $( ".tNBut" ).button({
    	icons: {
            primary: "ui-icon-circle-plus"
        },
        text: true
    }).click(function() {
		$( "#dialogPessoa-form" ).dialog( "option", "title", "Nova Pessoa" )
			.find("#isEdit").val("false");
		$( "#dialogPessoa-form" ).dialog( "open" );
	});

	$( ".delBut" ).button({
    	icons: {
            primary: "ui-icon-closethick"
        },
        text: true
    }).click(function() {
    	var anSelected 	= fnGetSelected( "#produtores" );
    	if ( anSelected.length == 1 ) {
    		var id_people = anSelected.find("td").first().html();
	    	var name = anSelected.find("td").first().next().html();
			queryDialog("Tem a certeza que quer remover <b>"+name+"</b> do sistema?<br><br>Esta pessoa ficará marcada na base de dados como eliminada.",
				function(){ // fires if yes
					$.post("../ajax/aj_deletePessoa.php","id_people="+id_people,
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
		var anSelected 	= fnGetSelected( "#produtores" );
        if ( anSelected.length == 1 ) {
        	var pointer = anSelected.find("td").first();
	    	var dg 		= $( "#dialogPessoa-form" );
			var id 		= pointer.html();
			var nome 	= pointer.next().html();
			var email 	= pointer.next().next().html();
			var phone = pointer.next().next().next().html();
			var concelho = pointer.next().next().next().next().html();
			var freguesia = pointer.next().next().next().next().next().html();
			var codigo_postal = pointer.next().next().next().next().next().next().html();
			//var flag = pointer.next().next().next().next().next().next().next().html();
			
			pointer = pointer.next().next().next().next().next().next().next();
			//var permissaoChecked = (pointer.html().indexOf("1")>=0);
			
			var adminChecked = (pointer.html().indexOf("a")>=0);
			var colabChecked = (pointer.html().indexOf("c")>=0);
			var produChecked = (pointer.html().indexOf("p")>=0);
			//var produChecked = (pointer.html().indexOf("P")>=0);
			pointer = pointer.next();
			var permissaoChecked = (pointer.html()=="1");
			pointer = pointer.next();
			var feriasChecked = (pointer.html()=="1");
			//var permissaoChecked = (pointer.html().indexOf("permissao")>=0);
			
			with (dg){
		    	dialog( "option", "title", "Editar Pessoa - #"+id );
		    	find("#isEdit").val(id);
		    	find("#name").val(nome);
		    	find("#email").val(email);
				find("#phone").val(phone);
		    	find("#concelho").val(concelho);
				find("#freguesia").val(freguesia);
				find("#codigo_postal").val(codigo_postal);
				//find("#flag").val(flag);
				
				
		    	
		    	find("#admin").prop('checked',adminChecked);
			find("#colab").prop('checked',colabChecked);
		    	find("#produ").prop('checked',produChecked);
			find("#permissao").prop('checked',permissaoChecked);
			find("#ferias").prop('checked',feriasChecked);
				dialog( "open" );
			}
        }
	});
	
	// dialog nova pessoa
	$( "#dialogPessoa-form" ).dialog({
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
					$.post("../ajax/aj_managePessoa.php",$(this).find("form").serialize(),
						function(data) {
							console.log("data="+data+"=");
							if(data.substr(data,1)=="{"){
								data = jQuery.parseJSON(data);
								$obj = $("<tr class='gradeA odd'><td>"+data.id+"</td><td>"+data.name+"</td><td>"+data.email+"</td><td>"+data.phone+"</td><td>"+data.concelho+"</td><td>"+data.freguesia+"</td><td>"+data.codigo_postal+"</td><td>"+data.flag+"</td><td>"+data.permissao+"</td><td>"+data.ferias+"</td></tr>");
								if(data.isEdit=="false"){
									$obj.prependTo("tbody");
								}else{
									$(".row_selected").replaceWith($obj);	
								}
								$( ".dialogForm" ).dialog( "close" );
							}else{
								if(data=="1062")	// duplicate error
									popupDialog("O E-mail ou o Telefone inseridos já existem na base de dados.<br><br>O número de telemóvel e o e-mail são únicos, pelo que não podem ser utilizados por mais do que uma pessoa.","error");
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
	
	<button id="editPessoaBut" class="edBut">Editar</button>
	<button id="deletePessoaBut" class="delBut">Remover</button>
</div>
<div id="dialogPessoa-form" class="dialogForm" title="Nova Pessoa">
	<form>
	<fieldset>
		<div>
			<label for="nome">Nome</label>
			<input type="text" name="name" id="name" title="Primeiro e Último" class="err_mandatory err_name text ui-widget-content ui-corner-all defaultText" />
		</div>
		<div>
			<label for="email">E-Mail</label>
			<input type="text" name="email" id="email" size="30" title="abc@dominio.com" class="err_mandatory err_email text ui-widget-content ui-corner-all defaultText" />
		</div>
		<div>
			<label for="telefone">Telefone</label>
			<input type="text" name="phone" id="phone" class="text ui-widget-content ui-corner-all" />
		</div>
		<div>
			<label for="concelho">Concelho</label>
			<input type="text" name="concelho" id="concelho" class="text ui-widget-content ui-corner-all" />
		</div>
        <div>
			<label for="freguesia">Freguesia</label>
			<input type="text" name="freguesia" id="freguesia" class="text ui-widget-content ui-corner-all" />
		</div>
        <div>
			<label for="codigo_postal">Codigo_Postal</label>
			<input type="text" name="codigo_postal" id="codigo_postal" class="text ui-widget-content ui-corner-all" />
		</div>       
		<div>
			<label for="funcoes">Funções</label>
			<div class="checkboxFormat">
				
                <input type="checkbox" id="admin" name="a"/><label for="admin" class="inline">Administrador</label><br>
                <input type="checkbox" id="colab" name="c"/><label for="colab" class="inline">Colaborador - (obrigatório!)</label><br>
		<input type="checkbox" id="produ" name="p"/><label for="produ" class="inline">Produtor</label>
			</div>
            <div>
			<label for="permissao">Permissão</label>
			<div class="checkboxFormat">
				<input type="checkbox" id="permissao" name="permissao"/><label for="permissao" class="inline">Acesso ao site</label>
			</div>
            <div>
			<label for="ferias">Ausente</label>
			<div class="checkboxFormat">
				<input type="checkbox" id="ferias" name="ferias"/><label for="ferias" class="inline">Ausente</label>
			</div>
		</div>
		<input type="hidden" id="isEdit" name="isEdit" value="false"/>
	</fieldset>
	</form>
</div>
        <h2>Lista de Pessoas</h2><hr>
          <div align="left" style="margin-left:10px; margin-right:10px; ">
          
			  <script>
           		$(function() {
            		$( "input[type=button]" )
              		.button()
              		$("editar").click(function( event ) {
               		event.preventDefault();
              		});
              
              		$( "input[type=button]" )
              		.button()
              		$("del").click(function( event ) {
                	event.preventDefault();
              		});
          		});
          </script>
          <?php // echo '<p style="color:red;" >'.$msg.'</p>'; ?>
          		<table cellpadding="0" cellspacing="0" border="0" class="display" id="produtores" width="100%">
    <thead>
        <tr>
            <th width="5%">#</th>
            <th width="40%">Nome</th>
            <th width="30%">E-mail</th>
            <th width="20%">Telefone</th>
            <th width="30%">Concelho</th>
            <th width="20%">Freguesia</th>
            <th width="30%">Codigo_Postal</th>
            <th width="20%">Tipo</th>
            <th width="30%">Acesso</th>
	    <th width="30%">Ausente</th>
          
           
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th>Id</th>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Telefone</th>
            <th>Concelho</th>
            <th>Freguesia</th>
            <th>Codigo_Postal</th>
            <th>Tipo</th>
            <th>Acesso</th>
            <th>Ausente</th>
           
        </tr>
    </tfoot>
</table>

      </div>
    	<br />
    	<br />
        </div>
      		<?php require_once("../includes/core/footer.php"); ?>
</div>
</body></html>
