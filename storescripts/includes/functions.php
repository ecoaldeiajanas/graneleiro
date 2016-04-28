<?
function isLoggedIn(){
	return($_SESSION && $_SESSION["pid"] && $_SESSION["pid"]>0);
}

function isAdmin(){
	return(isLoggedIn() && $_SESSION["admin"]);
}

function blockForNotAdmin(){
	if(!isAdmin()){
		displayLargeFailure("Esta página é do uso exclusivo dos colaboradores da Madeira Coop.<br><br>Se é um colaborador, tem de fazer login primeiro.");
		die();
	}
}

function displayLargeFailure($text){
	$s = '<div class="largeFailure">
		<p>'.$text.'</p></div>';
		echo $s;
}

function json_encode_dataTable($data){
	$customJSON = "{ \"aaData\":[";
	foreach ($data as $row => $rr) {
		$customJSON .= "[";
		foreach ($rr as $key => $value) {
			$customJSON .= "\"$value\",";
		}
		$customJSON = removeLastCharacter($customJSON);
		$customJSON .= "],";
	}
	$customJSON = removeLastCharacter($customJSON);
	$customJSON .= "]}";
	return utf8_encode($customJSON);
}

function removeLastCharacter($s){
	return substr($s, 0, strlen($s)-1);
}

?>