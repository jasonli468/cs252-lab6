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
    <script type='module' src='editpreset.js'></script>
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
        include 'api/dbconnect.php';
        $query = mysqli_prepare($con, "SELECT * FROM Presets WHERE User_ID = ? AND Nickname = ?");
        mysqli_stmt_bind_param($query, "is", $_SESSION['userID'], $_GET['nickname']);
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        mysqli_stmt_free_result($query);
        
        if($row = mysqli_fetch_assoc($result))
        {
            $checked = $row['Open'] ? 'checked' : '';
            echo "<h4 id='message' class='left'>Edit Preset $row[Nickname]:</h4>
                <form id='filters'>
                    <div id='searchMap'></div>
                    <div class='dropdownContainer'>
                        <br/>
                        <label>Maximum Distance:&nbsp</label>
                        <select id='distance'>";
            switch($row['Distance'])
            {
                case 804.672:
                    echo "<option value='804.672'>0.5 miles (0.8 km)</option>
                        <option value='1609.344'>1 mile (1.2 km)</option>
                        <option value='4032.36'>2.5 miles (4 km)</option>
                        <option value='8046.72'>5 miles (8 km)</option>
                        <option value='16093.44'>10 miles (16 km)</option>";
                        break;
                case 1609.344:
                    echo "<option value='804.672'>0.5 miles (0.8 km)</option>
                        <option value='1609.344' selected>1 mile (1.2 km)</option>
                        <option value='4032.36'>2.5 miles (4 km)</option>
                        <option value='8046.72'>5 miles (8 km)</option>
                        <option value='16093.44'>10 miles (16 km)</option>";
                        break;
                case 4032.36:
                    echo "<option value='804.672'>0.5 miles (0.8 km)</option>
                        <option value='1609.344'>1 mile (1.2 km)</option>
                        <option value='4032.36' selected>2.5 miles (4 km)</option>
                        <option value='8046.72'>5 miles (8 km)</option>
                        <option value='16093.44'>10 miles (16 km)</option>";
                        break;
                case 8046.72:
                    echo "<option value='804.672'>0.5 miles (0.8 km)</option>
                        <option value='1609.344'>1 mile (1.2 km)</option>
                        <option value='4032.36'>2.5 miles (4 km)</option>
                        <option value='8046.72' selected>5 miles (8 km)</option>
                        <option value='16093.44'>10 miles (16 km)</option>";
                        break;
                case 16093.44:
                    echo "<option value='804.672'>0.5 miles (0.8 km)</option>
                        <option value='1609.344'>1 mile (1.2 km)</option>
                        <option value='4032.36'>2.5 miles (4 km)</option>
                        <option value='8046.72'>5 miles (8 km)</option>
                        <option value='16093.44' selected>10 miles (16 km)</option>";
                        break;
            }
            
            echo "</select> <br/>
                    </div>
                    <input type='hidden' value='$row[Min_Price]' id='minPrice'>
                    <input type='hidden' value='$row[Max_Price]' id='maxPrice'>
                    <div class='sliderContainer'>
                        <label>Price Range: </label>
                        <div id='priceSlider'></div> <br/>
                    </div>
                    <br/>
                    <label><input type='checkbox' id='open' $checked>Restaurants Open Now</label>
                    <input id='submit' type='submit' value='Update Preset'>
                    <input type='hidden' value='$row[Latitude]' id='lat'>
                    <input type='hidden' value='$row[Longitude]' id='lng'>
                    <input type='hidden' value='$row[Nickname]' id='name'>
                </form>";
        }
        else
        {
            echo "ERROR: Preset not found.";
        }
    ?>
</div>

</body>