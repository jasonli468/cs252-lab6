<?php
    session_save_path('/home/campus/li2384/www/tmp');
    session_start();
    // If a login token cookie is found and the user is not already logged in, try to log in with the cookie
    if(isset($_COOKIE['login']) && !isset($_SESSION['userID']))
    {
        // Attemp to pull user data for the ID and token in the cookie
        include 'api/dbconnect.php';
        $tokenArray = explode(',', $_COOKIE['login']);
        $tokenHash = hash('sha256', $tokenArray[1]);
        $userQuery = mysqli_prepare($con, "SELECT * FROM Login_Tokens NATURAL JOIN Users WHERE User_ID = ? AND Token_Hash = ? AND Expiration_Date > NOW()");
        mysqli_stmt_bind_param($userQuery, "is", $tokenArray[0], $tokenHash);
        mysqli_stmt_execute($userQuery);
        $result = mysqli_stmt_get_result($userQuery);
        mysqli_stmt_free_result($userQuery);

        // If a user is found for this information, log in the user, delete the old token, and generate a new token. Refreshing tokens prevents access to user's accounts if their cookies are leaked
        if($row = mysqli_fetch_assoc($result))
        {
            // Save user data to log in the user
            $_SESSION['userID'] = $row['User_ID'];
            $_SESSION['distance'] = $row['Distance'];

            // Delete the old token
            $deleteQuery = mysqli_prepare($con, "DELETE FROM Login_Tokens WHERE User_ID = ? AND Token_Hash = ?");
            mysqli_stmt_bind_param($deleteQuery, "is", $tokenArray[0], $tokenHash);
            mysqli_stmt_execute($deleteQuery);
            mysqli_stmt_close($deleteQuery);

            // Create a new token and override teh cookie
            $newToken = bin2hex(mcrypt_create_iv(32));
            $tokenQuery = mysqli_prepare($con, "INSERT INTO Login_Tokens(User_ID, Token_Hash, Expiration_Date) VALUES (?, ?, ADDDATE(NOW(), 30))");
            mysqli_stmt_bind_param($tokenQuery, "is", $row['User_ID'], hash('sha256', $newToken));
            mysqli_stmt_execute($tokenQuery);
            mysqli_stmt_close($tokenQuery);
            setcookie('login', $row['User_ID'] . ',' . $newToken, time() + (60 * 60 * 24 * 30), "~li2384/cs252-lab6/", "", true, true);
        }
        // If no user found, assume hacking attempt with old token and delete all cookies for that user
        else
        {
            $deleteQuery = mysqli_prepare($con, "DELETE FROM Login_Tokens WHERE User_ID = ?");
            mysqli_stmt_bind_param($deleteQuery, "i", $tokenArray[0]);
            mysqli_stmt_execute($deleteQuery);
            mysqli_stmt_close($deleteQuery);
        }
        mysqli_free_result($result);
        mysqli_stmt_close($userQuery);
        mysqli_close($con);
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
    <script src='index.js'></script>
    <?php 
        include 'apikey.php';
        echo "<script async defer src='https://maps.googleapis.com/maps/api/js?key=$key'></script>";
    ?>
    
    <title>Home - Hungry but Indicisive Boiler</title>
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
    <h4 id='message'>Select your location on the map and options for your search to find a random restaurant to eat at!</h4>
    <form id='filters'>
        <div id='searchMap'></div>
        <label>Maximum Distance:&nbsp</label>
        <select id='distance'>
            <option value='804.672'>0.5 miles (0.8 km)</option>
            <option value='1609.344'>1 mile (1.2 km)</option>
            <option value='4032.36'>2.5 miles (4 km)</option>
            <option value='8046.72'>5 miles (8 km)</option>
            <option value='16093.44'>10 miles (16 km)</option>
        </select> <br/>
        <label>Price Range: </label>
        <div id='priceSlider'></div> <br/>
        <input type='submit' value='Search'>
    </form>
    <div id='place' class='hidden'>
        <div id='placeMap'></div>
        <div class='placeOverlay'>
            <h2 id='placeName'>Five Guys Burgers and Fries</h2>
            <div id='placeLabels'>
                Address: <br/>
                Rating: <br/>
                Price:
            </div>
            <div id='placeDetails'>
                <br/>
                <br/>
                
            </div>
            <img id='placeImg'>
        </div>
    </div>
</div>


</body>