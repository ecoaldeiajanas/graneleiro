<?php 
//Página protegida ao admin
ob_start();
session_start();

require_once("./inc/session_auth.php");
?>
<link href="../style/css/bootstrap.css" rel="stylesheet" type="text/css"/>
<style>
hr {
  margin: 60px 0 0 0;
}
.table-produtos td {
  padding: 5px 3px;
  border-bottom: 1px solid #EEE;
}
.table-produtos th {
  padding: 5px;
}
td, th {
  font-size: 70%;
}
</style>

<div id="motherContainer">
  <?php require_once("./inc/showEncomendasConsumidores.php"); ?>
  
</div>

</body></html>
