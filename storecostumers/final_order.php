<?php 
ob_start();
session_start();
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
//dados da encomenda
require_once("../storescripts/connectMysql.php");
if(isset($_GET['id'])){
	$targetID=$_GET['id'];
	
	$sql=mysql_query("SELECT * FROM encomenda, encomenda_has_products, products WHERE encomenda.id_encomenda='$targetID' AND encomenda_has_products.id_produto=products.id AND encomenda_has_products.id_encomenda='$targetID' ");

	while($row=mysql_fetch_array($sql)){
		  
		  $total=$row['total'];
		  $dateE=$row['date'];
		  $id_encomenda=$row['id_encomenda'];
	}
	$totalTaxa=$total+$total*10/100;
}	
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

<?php require_once("../includes/core/header.php"); ?>
<link href="../style/css/include/demo_table_jui.css" rel="stylesheet" type="text/css"/>
<link href="../style/css/include/demo_page.css" rel="stylesheet" type="text/css"/>

<!-- /////////////////////////////////////////////////////////////////////////////////// -->
<script>
$(function() {
var x = '<?php echo $targetID; ?>';
var z = '<?php echo $id_encomenda; ?>';
var y = '<?php echo $total; ?>';
	// build table
	$('#verFinalOrder').dataTable( {
			"bJQueryUI": true,
	        "sPaginationType": "full_numbers",
			"bPaginate": true,
			"bLengthChange": true,
	        "bProcessing": true,
			"bFilter": false,
	        "iDisplayLength": 50,
			
	        "sAjaxSource": "../ajax/aj_getVerFinalOrder.php?idEnc="+x,
	        "fnInitComplete": function(oSettings, json) {
		    	correctDataTable();
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
            <li><h1><a href="#">STOCK DA SEMANA</a></h1></li>
            <li><h1><a href="servicos.php">SERVIÇOS</a></h1></li>
	    <li><h1><a href="info.php">Info</a></h1></li>
            </ul>
            </div>                                   
            <div class="lastHeading">
                <div class="userOptions">
                    <div id="userOptionsInner">
                        	<h1 ><a href="ver_encomenda.php">VER ENCOMENDA</a></h1>
                                <?php if($admin){echo '<h1 ><a href="../storeadmin/index.php">ADMIN</a></h1>';}?>
                                <?php if($produtor){echo '<h1 ><a href="../storeprodutor/index.php">PRODUTOR</a></h1>';}?>
                                <h1 ><a href="logout.php">SAIR</a></h1>
                    </div>
                </div>
            </div>
        </div>

    
   <div id="headerDrawer">
   </div>
   
   <div id="pageContainer">
   	<?php echo "<h2> Encomenda realizada com sucesso! </h2><hr/>";?>
   		
            <div align="left" style="margin-left:24px; margin-right:24px; ">
            <div align="center" style="margin-left:24px ">
    		<br/>
   			<?php echo "<b> Local de Entrega: </b>".$local." - "."<b>Data: </b>".$date." - "."<b>Hora: </b>".$hora."<br/>"."<br/>"."<br/>";?>
            </div>
            <?php echo '<b>'.$_SESSION["name"].'</b> - '.$dateE.'';?>
           <br/><br/>
           <table cellpadding="0" cellspacing="0" border="0" class="display" id="verFinalOrder" width="100%">
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
        	
				<?php echo "<p align='right'><b>Total da Encomenda: ".number_format($total, 2)." - euros </b></p>";
				     echo "<p align='right'><b>Total da Encomenda+Taxa: <font style='color:red'>".number_format($totalTaxa, 2)." - euros </b></font></p>";
				echo "<p></br>* Ao preço total da encomenda está indexada uma taxa de 10% que ajudará na sustentabilidade do sistema.</p>" 
				?>
				<br/>
                </div>
    	</div>
   </div>

      <?php require_once("../includes/core/footer.php"); ?>
</div>
</body></html>
