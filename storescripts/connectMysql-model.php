<?php

require_once("includes/core/mysql4.php");

$conDB = new sql_db('DB_HOST','DB_USER','DB_PASSWORD','DB_NAME');
  
if(!$conDB){
echo "There was an error with connecting to the database.";
die();
}