<?php ob_start(); ?>
<?php
$emailEstado=0;
if(isset($_POST['email'])){

include"storescripts/connectMysql.php";

//pega a variavel via post
$email = $_POST['email'];
//busca no db o usuario com o email 
$sql= mysql_query("SELECT *  FROM people WHERE email='$email' ");

	// verificar se a pessoa existe
	$existCount = mysql_num_rows($sql);
	if($existCount==1){
	$emailEstado=1;
	$emailMsg="<br/><br/><li><b>Por favor aceda ao seu email para criar nova password.</b></li>";
	while($row=mysql_fetch_array($sql)){
		$name=$row["name"];
	}
			//email para o cliente
			$ownerEmail='graneleiro@ecoaldeiajanas.org';
	
			$to = $email;
			//$to='ajcfgomes@hotmail.com';
	
			$subject = 'Prossumidores - Recuperar Password.';
	
			$headers = "From: Prossumidores  <" . strip_tags($ownerEmail) . ">\n";
			$headers .= "Reply-To: ". strip_tags($ownerEmail) . "\r\n";
			//$headers .= "CC: susan@example.com\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=utf-8\r\n";
			$message = '<html><body>';
			$message .='<p>Viva '.$name.'!</p>';
			$message .='<p>Por favor click na ligação <u>Criar nova password</u></p>';
			$message .='<p><a href="http://www.graneleiro.ecoaldeiajanas.org/nova_pass.php?e='.$email.'"><ul><li>Criar nova password</a></li></ul></p>';
			
			$message .= '</body></html>'; 
			
			mail($to, $subject, $message, $headers);
					
}else{
@$msg="O endereço de e-mail não existe na base de dados";
        
}

}

?>
<?php require_once("includes/core/header1.php"); 

?> 
     <div id="motherContainer">
        <div id="header">
            <div id="smalllogo"></div>
            <!--<div class="heading"><h1><a href="index.php">QUEM SOMOS</a></h1></div>
            <div class="heading"><h1><a href="stock_semana.php?idCat=1">STOCK DA SEMANA</a></h1></div>
	   <div class="heading"><h1><a href="servicos.php">SERVIÇOS</a></h1></div>-->
<div class="heading">
<ul>
<li><h1><a href="index.php">QUEM SOMOS</a></h1></li>
           <!-- <li><h1><a href="stock_semana.php?idCat=1">STOCK DA SEMANA</a></h1></li>
	    <li><h1><a href="servicos.php">SERVIÇOS</a></h1></li>
	    <li><h1><a href="servicos.php">INFO</a></h1></li>-->
</ul>
</div>
            <div class="lastHeading">
                <div class="userOptions">
                    <div id="userOptionsInner">
                            <h1 ><a href="registar.php">REGISTAR</a></h1>
                            <h1 ><a href="login.php">LOGIN</a></h1>
                    </div>
                </div>
        </div>
    </div>
        
    <div id="headerDrawer">
    </div>
        
    <div id="pageContainer">
		<label for="title"><h2>Recuperar password</h2><hr/><br /></label>
	  	<div align="left" style="margin-left:24px ">
   	     
           <script>
			$(function() {
			$( "input[type=submit]" )
			.button()
			$("button").click(function( event ) {
			event.preventDefault();
				});
			});
  		  </script>

	  <?php  if($emailEstado==1){
		echo $emailMsg;
		}else{	
		
	 ?> 	
          <?php echo '<p style="color:red;" >'.@$msg.'</p>'; ?>
          <form id="form1" name="form1" method="post" action="recuperar_pass.php" >
          E-mail:<br/>
          <input name="email" type="text"id="email" size="40" class="required email"/>
          <br/><br/>
          
          <label>
          <input type="submit" name="button" id="button" value="Recuperar password" />
          </label>
          </form>
          <?php } ?>
    </div>
    </div>
      <?php require_once("includes/core/footer.php"); ?>
    </div>

</body></html>
