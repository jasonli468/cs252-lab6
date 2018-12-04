<?php
    include 'apikey.php';
    $location = $_GET['reference'];
    if(isset($key) && isset($reference))
    {
        header('Content-type: application/json');
        echo file_get_contents("https://maps.googleapis.com/maps/api/place/photo?maxwidth=440&maxheight=245&photoreference=$reference&key=$key");
    }
    else
    {
        echo "Invalid Request";
    }
?>