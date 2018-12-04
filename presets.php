<?php
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
    <script src="presets.js"></script>

    <title>Presets - Hungry but Indicisive Boiler</title>
    <link rel="stylesheet" type="text/css" href="style.css">
    <link rel="shortcut icon" type="image/png" href="favicon.ico"/>
</head>
<body>
<?php include 'header.php';?>

<div class="container">
    <h3 class='left'>Your Presets:</h3>
    <form action='createpreset.php'>
        <input type='submit' class='rightButton' value='Add New Preset'>
    </form>
    <div class='clear'>
        <?php
            // Get list of presets from DB
            include 'api/dbconnect.php';
            $query = mysqli_prepare($con, "SELECT Nickname FROM Presets WHERE User_ID = ? ORDER BY Nickname ASC");
            mysqli_stmt_bind_param($query, "i", $_SESSION['userID']);
            mysqli_stmt_execute($query);
            $result = mysqli_stmt_get_result($query);
            mysqli_stmt_free_result($query);

            // If any are found, set response status to success and add them all to the response
            if($row = mysqli_fetch_assoc($result))
            {
                do
                {
                    echo"<form class='clear' action='editpreset.php' id='$row[Nickname]'>
                        <div class='bigLeft'>$row[Nickname]</div>
                        <input type='hidden' name='nickname' value='$row[Nickname]'>
                        <input type='button' class='rightButton' value='Delete Preset'>
                        <input type='submit' class='rightButton' value='Edit Preset'>
                    </form>";
                } while($row = mysqli_fetch_assoc($result));
            }
            else
            {
                echo "No presets. To add a new preset, click the button in the top right under the header";
            }
            mysqli_free_result($result);
            mysqli_stmt_close($query);
            mysqli_close($con);
        ?>
    </div>
</div>
</body>