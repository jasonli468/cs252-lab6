<?php
    session_save_path('/home/campus/li2384/www/tmp');
    session_start();
    session_unset();
    session_destroy();
    if(isset($_COOKIE['login']))
    {
        include 'dbconnect.php';
        $tokenArray = explode(',', $_COOKIE['login']);
        $deleteQuery = mysqli_prepare($con, "DELETE FROM Login_Tokens WHERE User_ID = ? AND Token_Hash = ?");
        mysqli_stmt_bind_param($deleteQuery, "is", $tokenArray[0], hash('sha256', $tokenArray[1]));
        mysqli_stmt_execute($deleteQuery);
        mysqli_stmt_close($deleteQuery);
        mysqli_close($con);
        unset($_COOKIE['login']);
        setcookie('login', '', 1, "", "", true, true);
    }
    header("refresh:3;url=index.php" );
?>
<!DOCTYPE html>
<html lang = "en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    
    <title>Signed Out - Hungry but Indicisive Boiler</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php include 'header.php';?>
<div class='container'>
    You've been successfully signed out. Returning to the home page in 3 seconds...
</div>
</body>