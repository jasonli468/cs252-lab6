<?php
    // Ensure the user is connectiong through HTTPS, otherwise redirect and kill this connection
    if($_SERVER["HTTPS"] == "off")
    {
        header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
        exit();
    }
    session_save_path('/home/campus/li2384/www/tmp');
    session_start();
    // If a login token cookie is found and the user is not already logged in, try to log in with the cookie
    if(isset($_COOKIE['login']) && !isset($_SESSION['userID']))
    {
        // Attemp to pull user data for the ID and token in the cookie
        include 'api/dbconnect.php';
        $tokenArray = explode(',', $_COOKIE['login']);
        $tokenHash = hash('sha256', $tokenArray[1]);
        $userQuery = mysqli_prepare($con, "SELECT UserID FROM Login_Tokens NATURAL JOIN Users WHERE User_ID = ? AND Token_Hash = ? AND Expiration_Date > NOW()");
        mysqli_stmt_bind_param($userQuery, "is", $tokenArray[0], $tokenHash);
        mysqli_stmt_execute($userQuery);
        mysqli_stmt_store_result($query);
        mysqli_stmt_bind_result($query, $userID);

        // If a user is found for this information, log in the user, delete the old token, and generate a new token. Refreshing tokens prevents access to user's accounts if their cookies are leaked
        if(mysqli_stmt_fetch($query))
        {
            // Save user data to log in the user
            $_SESSION['userID'] = $userID;

            // Delete the old token
            $deleteQuery = mysqli_prepare($con, "DELETE FROM Login_Tokens WHERE User_ID = ? AND Token_Hash = ?");
            mysqli_stmt_bind_param($deleteQuery, "is", $tokenArray[0], $tokenHash);
            mysqli_stmt_execute($deleteQuery);
            mysqli_stmt_close($deleteQuery);

            // Create a new token and override teh cookie
            $newToken = bin2hex(mcrypt_create_iv(32));
            $tokenQuery = mysqli_prepare($con, "INSERT INTO Login_Tokens(User_ID, Token_Hash, Expiration_Date) VALUES (?, ?, ADDDATE(NOW(), 30))");
            mysqli_stmt_bind_param($tokenQuery, "is", $userID, hash('sha256', $newToken));
            mysqli_stmt_execute($tokenQuery);
            mysqli_stmt_close($tokenQuery);
            setcookie('login', $userID . ',' . $newToken, time() + (60 * 60 * 24 * 30), "~li2384/cs252-lab6/", "", true, true);
        }
        // If no user found, assume hacking attempt with old token and delete all cookies for that user
        else
        {
            $deleteQuery = mysqli_prepare($con, "DELETE FROM Login_Tokens WHERE User_ID = ?");
            mysqli_stmt_bind_param($deleteQuery, "i", $tokenArray[0]);
            mysqli_stmt_execute($deleteQuery);
            mysqli_stmt_close($deleteQuery);
        }
        mysqli_free_result($query);
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
    <script type='module' src='index.js'></script>
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
    <h4 id='message' class='left'>Select your location, choose your options, and hit Search to get a random restaurant to eat at!</h4>
    <form id='filters'>
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
        <label><input type='checkbox' id='open' checked> Restaurants Open Now</label>
        <?php
            if(isset($_SESSION['userID']))
            {
                // Get list of presets from DB
                include 'api/dbconnect.php';
                $query = mysqli_prepare($con, "SELECT Nickname, Latitude, Longitude, Distance, Min_Price, Max_Price, Open FROM Presets WHERE User_ID = ? ORDER BY Nickname ASC");
                mysqli_stmt_bind_param($query, "i", $_SESSION['userID']);
                mysqli_stmt_execute($query);
                mysqli_stmt_store_result($query);
                mysqli_stmt_bind_result($query, $nickName, $lat, $long, $dist, $minPrice, $maxPrice, $open);

                // If any are found, set response status to success and add them all to the response
                if(mysqli_stmt_fetch($query))
                {
                    $presets = array();
                    echo "<label class='marginLeft'>Preset: &nbsp</label><select id='preset'><option value=''>None</option>";
                    do
                    {
                        $presets[] = array("Nickname" => $nickName,
                                           "Latitude" => $lat,
                                           "Longitude" => $long,
                                           "Distance" => $dist,
                                           "Min_Price" => $minPrice,
                                           "Max_Price" => $maxPrice,
                                           "Open" => $open);
                        echo "<option value='$nickName'>$nickName</option>";
                    } while($row = mysqli_fetch_assoc($result));
                    echo "</select>";

                    foreach($presets as $preset)
                    {
                        echo "<input type='hidden' id='$preset[Nickname]Lat' value='$preset[Latitude]'>
                            <input type='hidden' id='$preset[Nickname]Lng' value='$preset[Longitude]'>
                            <input type='hidden' id='$preset[Nickname]Dist' value='$preset[Distance]'>
                            <input type='hidden' id='$preset[Nickname]MinPrice' value='$preset[Min_Price]'>
                            <input type='hidden' id='$preset[Nickname]MaxPrice' value='$preset[Max_Price]'>
                            <input type='hidden' id='$preset[Nickname]Open' value='$preset[Open]'>";
                    }
                }
                mysqli_free_result($query);
                mysqli_stmt_close($query);
                mysqli_close($con);
            }
        ?>
        <input type='submit' value='Search'>
    </form>
    <div id='place' class='hidden'>
        <?php if(isset($_SESSION['userID'])) echo "<input type='button' value='Blacklist Restaurant' class='rightButton' id='blacklist' title='Ignore restaurant in future search results'>" ?>
        <input type='button' class='rightButton' value='Show Another Result' id='showAnother'>
        <span style='float:right' class='hidden' id='showAnotherApology'>Google limits me to 20 results per search (or your filters are too restrictive), please search again!</span>
        <div id='placeMap'></div>
        <div class='placeOverlay'>
            <h2 id='placeName'></h2>
            <div id='placeLabels'>
                Address: <br/>
                Rating: <br/>
                Pricing:
            </div>
            <div id='placeDetails'></div>
            <div id='placeImgDiv'><img id='placeImg'></div>
        </div>
    </div> <br/>
    <input class='hidden' type='button' value='Back' id='back'>
</div>

</body>
