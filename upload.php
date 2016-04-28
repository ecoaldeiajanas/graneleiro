<?php

// print_r($_FILES);
// echo "got files";

foreach ($_FILES["images"]["error"] as $key => $error) {  
    if ($error == UPLOAD_ERR_OK) {  
        $name = $_FILES["images"]["name"][$key];
        $name = explode(".",$name);
        $name = $name[0].dechex(round(rand(0,254))).".".$name[1]; // "prevents" that files are overwritten
        move_uploaded_file( $_FILES["images"]["tmp_name"][$key], "p_images/" . $name); 
        echo $name;
        die();
    }
    echo "ERROR";
}  

?>
