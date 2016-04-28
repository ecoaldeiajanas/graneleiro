<?php
// autentica o user como admin e verifica se existe encomenda da semana para processar.
require_once("../storescripts/connectMysql.php");
$logged = ($_SESSION["id"] && $_SESSION["id"] != null);

if ($logged) {
  $id         = $_SESSION["id"];
  $name       = $_SESSION["name"];
  $firstName  = explode(" ",$name); $firstName = $firstName[0];
  $admin      = $_SESSION["manager"];
  $produtor   = $_SESSION["produtor"];
} else {
  header("location:../login.php");
  exit();
}

//Verificar se existe encomenda da semana
$id_people = $_SESSION["id"];
$sql = mysql_query("SELECT id_people, semana FROM encomenda WHERE id_people=$id_people AND semana=0  LIMIT 1");
$existEnc = mysql_num_rows($sql);

//Verificar se site está bloqueado
$sql = mysql_query("SELECT * FROM block LIMIT 1");
while ($row = mysql_fetch_array($sql)) {
  $block = $row['block'];
}
if ($block == 0) {
  $link = "<font color='orange'><b>Fechado  >> </b></font> <a href='bloquearAbrir.php'> Alterar estado</a>";
} else {
  $link = "<font color='orange'><b>Aberto >> </b></font><a href='bloquearAbrir.php'> Alterar estado</a>"; 
}
?>
