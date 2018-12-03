<?php
    include 'apikey.php';
    $location = $_GET['location'];
    $radius = $_GET['radius'];
    if(isset($key) && isset($location) && isset($radius))
    {
        header('Content-type: application/json');
        echo file_get_contents("https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$location&radius=$radius&type=restaurant&opennow=true&key=$key");
    }
    else
    {
        echo "Invalid Request";
    }
?>