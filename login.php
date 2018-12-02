<?php
    session_save_path('/home/campus/li2384/www/tmp');
    session_start();
?>
<!DOCTYPE html>
<html lang = "en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="login.js"></script>

    <title>Log In - Hungry but Indicisive Boiler</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php include 'header.php';?>

<div class="container">
    <form id="loginForm">
        <label>Email</label>
        <input id="emailInput" type="email" placeholder="Enter Email" required><br/><br/>
        <label>Password</label>
        <input id="passwordInput" type="password" placeholder="Enter Password" required><br/><br/>
        <input type="submit" value="Login"/><br/>
        <label class='normal'><input type="checkbox" id="remember"> Remember me</label>
        <span class="right"><a href="forgotpassword.php">Forgot password?</a></span>
    </form>
    <span id="statusMessage"></span>
</div>
</body>