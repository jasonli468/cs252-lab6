<?php
    // Run a search through Google Maps' Nearby Search API
    include 'apikey.php';
    $location = $_GET['location'];
    $radius = $_GET['radius'];
    $minPrice = $_GET['minPrice'];
    $maxPrice = $_GET['maxPrice'];
    $open = $_GET['open'];
    if(isset($key) && isset($location) && isset($radius) && isset($minPrice) && isset($maxPrice) && isset($open))
    {
        $open = $open === 'true' ? '&opennow=true' : ''; // Having opennow as a parameter at all sets it to true, don't include it if it's not set
        header('Content-type: application/json');
        echo file_get_contents("https://maps.googleapis.com/maps/api/place/nearbysearch/json?location=$location&radius=$radius&minprice=$minPrice&maxprice=$maxPrice&type=restaurant$open&key=$key");
    }
    else
    {
        echo "Invalid Request";
    }
?>