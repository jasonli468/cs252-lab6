<?php
    // If the user is not logged in, redirect to login page
    session_save_path('/home/campus/li2384/www/tmp');
    session_start();
    if(!isset($_SESSION['userID']))
    {
        header("Location: login.php");
        exit();
    }

    // Get location of user based on the connection IP address
    $ip = $_SERVER['REMOTE_ADDR'];
?>

<!DOCTYPE html>
<html lang = "en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script type='module' src='createpreset.js'></script>
    <?php 
        include 'apikey.php';
        echo "<script async defer src='https://maps.googleapis.com/maps/api/js?key=$key'></script>";
    ?>
    
    <title>New Preset - Hungry but Indicisive Boiler</title>
    <link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="shortcut icon" type="image/png" href="favicon.ico"/>
</head>
<body>
<?php
    include 'header.php';
?>
<div class='container'>

<input type='hidden' value="<?php echo $ip;?>" id='ip'>
    <?php

        echo "<h4 id='message' class='left'>Edit:</h4>
        <form id='filters'>
            <input type='text' id='name' placeholder='Enter Preset Name'><br/><br/>
            <div id='searchMap'></div>
            <div class='dropdownContainer'>
                <br/>
                <label>Maximum Distance:&nbsp</label>
                <select id='distance'>
                    <option value='804.672'>0.5 miles (0.8 km)</option>
                    <option value='1609.344'>1 mile (1.2 km)</option>
                    <option value='4032.36'>2.5 miles (4 km)</option>
                    <option value='8046.72'>5 miles (8 km)</option>
                    <option value='16093.44'>10 miles (16 km)</option>
                </select> <br/>
            </div>
            <div class='sliderContainer'>
                <label>Price Range: </label>
                <div id='priceSlider'></div> <br/>
            </div>
            <br/>
            <label><input type='checkbox' id='open' checked>Restaurants Open Now</label>
            <input id='submit' type='submit' value='Search'>
        </form>";
    ?>
</div>

</body>