<!DOCTYPE html>
<html lang = "en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="forgotpassword.js"></script>
    
    <title>Forgot Password - Hungry but Indicisive Boiler</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php include 'header.php';?>
<div class="container">
    <h2>Forgot Password</h2>
    <form id="emailForm">
        <label>Email:</label>
        <input id="emailInput" type="email" name="Email" placeholder="Please enter your email"><br/><br/>
        <input type="submit" value="Send Reset Password Email">
    </form>
    <span id="statusMessage"><br/></span>
</div>

</body>