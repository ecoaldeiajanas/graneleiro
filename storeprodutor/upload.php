<?php

function imageCreateFromAny($filepath)
{
    $type = exif_imagetype($filepath); // [] if you don't have exif you could use getImageSize()
    $allowedTypes = array(
        1,  // [] gif
        2,  // [] jpg
        3,  // [] png
        6   // [] bmp
    );
    if (!in_array($type, $allowedTypes)) {
        return false;
    }
    switch ($type) {
        case 1:
            $im = imageCreateFromGif($filepath);
            break;
        case 2:
            $im = imageCreateFromJpeg($filepath);
            break;
        case 3:
            $im = imageCreateFromPng($filepath);
            break;
        case 6:
            $im = imageCreateFromBmp($filepath);
            break;
    }
    return $im;
}
// print_r($_FILES);
// echo "got files";

foreach ($_FILES["images"]["error"] as $key => $error) {
    if ($error == UPLOAD_ERR_OK) {
        $name = $_FILES["images"]["name"][$key];
        $name = explode(".", $name);
        $name = $name[0].dechex(round(rand(0, 254))).".".$name[1]; // "prevents" that files are overwritten

        // Move the image file to images folder
        if (move_uploaded_file($_FILES["images"]["tmp_name"][$key], "../p_images/" . $name)) {
            // Get original uploaded file params
            $img_orig = "../p_images/" . $name;
            list($width_orig, $height_orig) = getimagesize($img_orig);
            $ratio_orig = $width_orig/$height_orig;

            // Define new width size (500px)
            $width_rsz = 500;
            $heigth_rsz = 0;

            // Only resize if uploaded image is wider than 500px
            if ($width_orig > $width_rsz) {
                // Calculate the new hiegth
                $heigth_rsz = $width_rsz/$ratio_orig;

                // Create a resized copy of uploaded image
                $image_rsz = imagecreatetruecolor($width_rsz, $heigth_rsz);
                $image = imageCreateFromAny($img_orig);
                imagecopyresampled($image_rsz, $image, 0, 0, 0, 0, $width_rsz, $heigth_rsz, $width_orig, $height_orig);

                // New name for the resized copy
                $image_rsz_new_name = explode(".", $name);
                $image_rsz_new_name = $image_rsz_new_name[0]."-rsz".".".$image_rsz_new_name[1];

                // Save the resized copy as jpg
                if (imagejpeg($image_rsz, "../p_images/" . $image_rsz_new_name, 100)) {
                    // If the resized copy is created and saved delete the uploaded image
                    unlink($img_orig);

                    echo $image_rsz_new_name;
                    die;
                } else {
                    echo $img_orig;
                    die();
                }
            } else {
                echo $img_orig;
                die();
            }
        }
    }
    echo "ERROR";
}
