<?php
    include 'apikey.php';
    $reference = $_GET['reference'];
    if(isset($key) && isset($reference))
    {
        header('Content-type: application/json');
        echo file_get_contents("https://maps.googleapis.com/maps/api/place/photo?maxheight=272&photoreference=$reference&key=$key");
    }
    else
    {
        echo "Invalid Request";
    }
?>