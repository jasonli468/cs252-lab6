<?php
    // If the user is not logged in, redirect to login page
    session_save_path('/home/campus/li2384/www/tmp');
    session_start();
    if(!isset($_SESSION['userID']))
    {
        header("Location: login.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang = "en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="blacklist.js"></script>

    <title>Blacklist - Hungry but Indicisive Boiler</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="shortcut icon" type="image/png" href="favicon.ico"/>
</head>
<body>
<?php include 'header.php';?>

<div class="container">
    <h3>Your Blacklisted Places:</h3>
    <?php
        // Get list of blacklisted places from DB
        include 'api/dbconnect.php';
        $query = mysqli_prepare($con, "SELECT Name, Place_ID FROM Blacklist WHERE User_ID = ? ORDER BY Name ASC");
        mysqli_stmt_bind_param($query, "i", $_SESSION['userID']);
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        mysqli_stmt_free_result($query);

        // If any are found, set response status to success and add them all to the response
        if($row = mysqli_fetch_assoc($result))
        {
            do
            {
                echo"<div class='clear' id='$row[Place_ID]'>
                    <div class='bigLeft'><a href='https://www.google.com/maps/search/?api=1&query=Google&query_place_id=$row[Place_ID]'>$row[Name]</a></div>
                    <input type='button' class='rightButton' value='Remove from Blacklist'>
                </div>";
            } while($row = mysqli_fetch_assoc($result));
        }
        else
        {
            echo "No blacklisted places. To add a place to your blacklist, click on the Blacklist button after searching for a place";
        }
        mysqli_free_result($result);
        mysqli_stmt_close($query);
        mysqli_close($con);
    ?>
</div>
</body>