<?php
ob_start();
session_start();
require_once("../includes/core/header.php"); 
require_once("../storescripts/connectMysql.php");

    $logged = ($_SESSION["id"] && $_SESSION["id"]!=null);
    if($logged){
        $id = $_SESSION["id"];
        $name = $_SESSION["name"];
        $firstName = explode(" ",$name); $firstName = $firstName[0];
        $admin = $_SESSION["manager"];
	$produtor = $_SESSION["produtor"];
	$email = $_SESSION["email"];
    	}else{
	header("location:../login.php");
	exit();
	}    
?>
<?php 
//Verificar se existe encomenda da semana
	 $sql=mysql_query("SELECT id_people, semana FROM encomenda WHERE id_people=$id AND semana=0  LIMIT 1");
		$existEnc=mysql_num_rows($sql);
?>

<?php
//Ver Local de Entrega
 	$sql=mysql_query("SELECT * FROM delivery ");
		while($row=mysql_fetch_array($sql)){
		$local=$row["local"];
		$date=$row['date'];
		$hora=$row['hora'];
	}
?>

<?php 
//Adicionar Produto a encomenda
$cartTotal='';
$obs="";
$sendEmail='<br/><a href="ver_encomenda.php?cmd=1">Desejo receber actualização da Encomenda Semanal no meu E-mail.</a>';
if(isset($_POST['pid'])){

		$id_encomenda=$_POST['id_encomenda'];
		$pid=$_POST['pid'];
		$id_produtor=$_POST['id_produtor'];
		$quant=$_POST['quantity'];
		$price=$_POST['price'];
		$cartTotal=$_POST['total'];
		$quantidade=$_POST['quantidade'];
		if($quant<=$quantidade){
				$quant=$quant;
				$obs="";
			}else{
				$quant=$quantidade;
				$obs="Quantidade disponivel.";

			}
		$priceTotal=$price*$quant;
		$cartTotal=$priceTotal+$cartTotal;
		$quantidade=$quantidade-$quant;
		
	//Inserir Produto na encomenda	
	$q = $conDB->sql_query("INSERT INTO encomenda_has_products(id_encomenda,id_produto,id_produtor,quant)
						VALUES('$id_encomenda','$pid','$id_produtor','$quant')", BEGIN_TRANSACTION); 
						
	//Actualizar Total da Encomenda					
	$q = $conDB->sql_query("UPDATE encomenda SET total='$cartTotal' WHERE id_encomenda='$id_encomenda'", BEGIN_TRANSACTION);
	//Actualizar a Quantidade em Stock
	$q = $conDB->sql_query("UPDATE products SET obs='$obs', quantidade='$quantidade' WHERE id='$pid'", BEGIN_TRANSACTION);
						
	$q = $conDB->sql_query("",END_TRANSACTION);
	
	if(!q) die();
	
	header("location:ver_encomenda.php?msg=1");
		exit();
	
}
?>

<?php
//Dados da Encomenda
	//Id do Cliente
	$targetID=$_SESSION['id'];

	$sql=mysql_query("SELECT * FROM encomenda, encomenda_has_products, products WHERE encomenda.id_people='$targetID' AND encomenda_has_products.id_produto=products.id AND encomenda_has_products.id_encomenda=encomenda.id_encomenda AND encomenda.semana=0 ORDER BY product_name ASC");

		while($row=mysql_fetch_array($sql)){
			$id_encomenda=$row["id_encomenda"];
			$total=$row['total'];
		}
?>

<?php
//Enviar email
if(isset($_GET['cmd'])){
	//Buscar encomenda
	$targetEnc=$id_encomenda;
	$dynamicList='';
	
	$sql=mysql_query("SELECT * FROM encomenda, encomenda_has_products, products WHERE encomenda.id_encomenda='$targetEnc' AND encomenda_has_products.id_produto=products.id AND encomenda_has_products.id_encomenda='$targetEnc' ");

	while($row=mysql_fetch_array($sql)){
		  $id_encomenda=$row["id_encomenda"];
		  $product_name=$row['product_name'];
		  $quantidade=$row['quant'];
		  $total=$row['total'];
		  $date=$row['date'];
		  $price=$row['price'];
		  $pid=$row['id'];
		  $produtor=$row['id_produtor'];
		  $details=$row['details'];
		  $unit=$row['unit'];
		  $peso=$row['peso'];
		  if($peso=='1'){
			  $precoTotal=$price*0;
		  }else{
		  $precoTotal=$price*$quantidade;
		  }
		  //Lista Dinamica
		  $dynamicList.='<tr>';
		  $dynamicList.='<td class="cart">'.$product_name.'</th>';
		  if($peso=='1'){
		  	$dynamicList.='<td style="color:red" class="cart">Necessário aferir o peso e acertar o valor</th>';
		  }else{
			$dynamicList.='<td class="cart"></th>';
		  }
		  $dynamicList.='<td class="cart" align="center"> '.$price.' €</td>';
		  if($unit=='1' || $peso=='1'){
		  	$dynamicList.='<td class="cart" align="center"> '.number_format($quantidade,0).'-Unid.</th>';
		  }else{
		    $dynamicList.='<td class="cart" align="center"> '.$quantidade.'-Kg</th>';
		  }
			
			$dynamicList.='<td class="cart" align="center"> '.number_format($precoTotal, 2).' €</td>';  
		
       	$dynamicList.='</tr>';
	}
	///////////////////////////////////////////////////////////////////////////////
	//email to the guest
	
	$ownerEmail='graneleiro@ecoaldeiajanas.org';
	//$array=(explode(" ",$name));
	//$firstName=$array[0];
	
	$totalTaxa=$total+$total*10/100;

	$to = $email;
	
	$subject = utf8_decode('Prossumidores - Encomenda Semanal - Actualização');
	
	$headers = "From: Prossumidores  <" . strip_tags($ownerEmail) . ">\n";
	$headers .= "Reply-To: ". strip_tags($ownerEmail) . "\r\n";
	//$headers .= "CC: susan@example.com\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=utf-8\r\n";
	
	$message = '<html><body>';
	$message .='<p>Viva '.$firstName.'!</p>';
	$message .='<p>A seu pedido enviamos actualização da sua Encomenda Semanal.</p>';
	$message .= '<table width="80%" border="1" cellspacing="2" cellpadding="0">';
	$message .=' <tr>';
	$message .='<th width="18%" class="prod" align="left" bgcolor="#c5cda8" scope="col"><b>Produto</b></th>';
	$message .='<th width="45%" align="left" bgcolor="#c5cda8" class="prod" scope="col"><b>Observações</b></th>';
	$message .='<th width="13%" class="prod" bgcolor="#c5cda8" scope="col"><b>Preço Unit.</b></th>';
	$message .='<th width="13%" class="prod" align="center" bgcolor="#c5cda8" scope="col"><b>Quantidade</b></th>';
	$message .='<th width="11%" class="prod" bgcolor="#c5cda8" scope="col"><b>Total</b></th>';
	$message .='</tr>'; 
	$message .=  '<tr>'.$dynamicList.'</tr>';
	$message .= '</table>';
	$message .='<br/><p align="left"><b>Total da Encomenda: '.number_format($total, 2).' - euros </b></br><b>Total da Encomenda+Taxa: <font style="color:red">'.number_format($totalTaxa, 2).' - euros </b></font></br>* Ao preço total da encomenda está indexada uma taxa de 10% que ajudará na sustentabilidade do sistema.</p>';
	$message .= '</body></html>'; 
	
	mail($to, $subject, $message, $headers);
	
	header("location:ver_encomenda.php?msg=2");
		exit();
}
 ?> 

<?php 
require_once("../includes/core/header.php"); 
require_once("../includes/dialogs.php"); 
?> 
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
var x = "<?php echo $targetID; ?>";
var z = '<?php echo $id_encomenda; ?>';
var y = '<?php echo $total; ?>';
	// build table
	$('#verEncomenda').dataTable( {
			"bJQueryUI": true,
	        "sPaginationType": "full_numbers",
			"bPaginate": true,
			"bLengthChange": true,
	        "bProcessing": true,
			"bFilter": false,
	        "iDisplayLength": 10,
			
	        "sAjaxSource": "../ajax/aj_getVerEncomenda.php?idEnc="+x,
	        "fnInitComplete": function(oSettings, json) {
		    	correctDataTable();
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
		iTotalMarket10=iTotalMarket+(iTotalMarket*10/100);

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
			self.location='stock_semana.php?add=1&idEnc='+z+'&t='+y;
		
	});

	$( ".delBut" ).button({
    	icons: {
            primary: "ui-icon-closethick"
        },
        text: true
    }).click(function() {
  		var anSelected 	= fnGetSelected( "#verEncomenda" );
    	if ( anSelected.length == 1 ) {
    		var id_produto = anSelected.find("td").first().html();
	    	var produto = anSelected.find("td").first().next().html();
			var quant = anSelected.find("td").first().next().next().next().next().html();
			var id_encomenda = z;
			var total = y;
			// Initialize your table
    		var oTable = $('#verEncomenda').dataTable();
    		// Get the length
   			 var count = oTable.fnGetData().length;
			if(count==1){
				popupDialog("<b>A Encomenda tem de ter no minimo um produto.","error");
			}else{
			queryDialog("Tem a certeza que quer remover o Produto <b>"+produto+"</b> da Encomenda?<br>",
		
				function(){ // fires if yes
					$.post("../ajax/aj_deleteVerEncomenda.php","id_produto="+id_produto+"&id_encomenda="+id_encomenda+'&total='+total+'&quant='+quant,
						function(data) {
							console.log("data="+data+"=");
							if(data.substr(data,1)=="{"){
								anSelected.remove();
								self.location='ver_encomenda.php?msg=1';
								//location.reload();
							}else{
								popupDialog("<b>A operação não foi bem sucedida</b><br><br>Occorreu um erro de comunicação ou sintaxe para com a base de dados. Contacte o administrador.","error");
							}
						}
					);
				}
			);
        }}
	});

	$( ".edBut" ).button({
    	icons: {
            primary: "ui-icon-wrench"
        },
        text: true
    }).click(function() {
		
		var anSelected 	= fnGetSelected( "#VerEncomenda" );
        if ( anSelected.length == 1 ) {
        	var pointer = anSelected.find("td").first();
	    	var dg 		= $( "#dialogVerEncomenda-form" );
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
	$( "#dialogVerEncomenda-form" ).dialog({
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
					$.post("../ajax/aj_manageVerEncomenda.php",$(this).find("form").serialize(),
						function(data) {
							console.log("data="+data+"=");
							if(data.substr(data,1)=="{"){
								self.location='ver_encomenda.php?msg=1';
								//location.reload();
							}else{
								/*if(data=="1062")	// duplicate error
									popupDialog("O produto que inseriu já existe na base de dados.<br><br>Os produtos não podem ter nomes iguais. Use uma designação extra para o produto. Por exemplo 'Batata Nova', 'Batata Normal'","error");
								else*/ if(data=="1205")	// duplicate error
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
  	    <li><h1><a href="index.php">QUEM SOMOS</a></h1></li>
            <?php if($existEnc>0){?>
            <li><h1><a href="#">STOCK DA SEMANA</a></h1></li>
                 <?php }else{?>
                 <li><h1><a href="stock_semana.php?idCat=1">STOCK DA SEMANA</a></h1></li>
                 <?php }?>
           <li><h1><a href="servicos.php">SERVIÇOS</a></h1></li>
	   <li><h1><a href="info.php">Info</a></h1></li>
           </ul>
           </div>
            <div class="lastHeading">
                <div class="userOptions">
                     <div id="userOptionsInner">
                        	<?php if($existEnc==1){
								echo '<h1 ><a href="ver_encomenda.php">VER ENCOMENDA</a></h1>';
							}else{
								echo '<h1 ><a href="cart.php">O MEU CABAZ</a></h1>';
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


	<div id="headerDrawer">
    </div>

	<div id="pageContainer">
 
    <div class="navBar">
            <button id="novoProdutoBut" class="nvBut">Adicionar Produto</button>
            <button id="editPessoaBut" class="edBut">Alterar Quantidade</button>
            <button id="deletePessoaBut" class="delBut">Remover Produto</button>
		</div>
   			<div id="dialogVerEncomenda-form" class="dialogForm" title="Nova Categoria">
        <form>
        <fieldset>
		<div>
			<label for="quant">Quantidade</label>
			<input type="text" name="quant" id="quant" size="15" title="Exemplo - 1.50" /> Kg/Unid.
		</div>
		<input type="hidden" id="isEdit" name="isEdit" value="false"/>
        <input type="hidden" id="id_encomenda" name="id_encomenda" value="<?php echo $id_encomenda; ?>"/>
        </fieldset>
        </form>
	</div>

    		<h2>Encomenda Semanal - <?php echo $_SESSION["name"]; ?></h2><hr>	
    	
    	
        	<br/>
          <div align="left" style="margin-left:24px; margin-right:24px; ">
          <div align="center" style="margin-left:24px ">
            <?php echo "<b> Local de Entrega: </b>".$local." - "."<b>Data: </b>".$date." - "."<b>Hora: </b>".$hora."<br/>"."<br/>";?>
	
            </div>
            <table cellpadding="0" cellspacing="0" border="0" class="display" id="verEncomenda" width="100%">
   				 <thead>
        		 <tr>
                	<th width="3%">#</th>
                   	<th width="20%">Produto</th>
                    <th width="29%">Observações</th>
                    <th width="14%">Preço Unit.</th>
                    <th width="10%">Quantidade</th>
                    <th width="14%">Preço Total</th>
       		     </tr>
    			 </thead>
    			 <tfoot>
                 <tr>
                 	<th>#</th>
                    <th>Produto</th>
                    <th>Observações</th>
                    <th>Preço Unit.</th>
                    <th>Quantidade</th>
                    <th>Preço Total</th>
        		</tr>
    			</tfoot>
			  </table>
              <br/>
				<p id="total"> </p>
				<p id="totalTaxa"> </p>
				<p>* Ao preço total da encomenda está indexada uma taxa de 10% que ajudará na sustentabilidade do sistema.</p> 
                  <?php if(isset($_GET['msg'])){
				if($_GET['msg']==1){
				  echo $sendEmail;
				}elseif($_GET['msg']==2){
				  echo "<br/>E-mail enviado com Sucesso!";
			       }
			} ?>
			
            </div>
      </div>
	</div>
      <?php require_once("../includes/core/footer.php"); ?>
</div>
</body></html>
