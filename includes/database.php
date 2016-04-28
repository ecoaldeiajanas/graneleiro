<?PHP
require_once("core/mysql4.php");

$conDB = new sql_db("localhost", "root", "", "cooperativa");

if(!$conDB){
	echo "There was an error with connecting to the database.";
	die();
}
?>