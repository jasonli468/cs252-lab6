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
    <script src="signup.js"></script>
    
    <title>Sign Up - Hungry but Indicisive Boiler</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php include 'header.php';?>

<div class="container">
    <h2>Sign Up</h2>
    <form id="signUpForm">
        <label>Email:</label> 
        <input id="emailInput" type="email" placeholder="Enter Email" required><span id="emailMessage"><br/></span><br/>
		<label>Confirm Email:</label> 
		<input id="emailConfirmInput" type="email" placeholder="Confirm Email" required><span id="emailConfirmMessage"><br></span><br class='bigbr'/>
        <label>Password:</label> 
        <input id="passwordInput" type="password" placeholder="Enter Password" required><span id="passwordMessage"><br/></span><br/>
		<label>Confirm Password:</label> 
		<input id="passwordConfirmInput" type="password" placeholder="Confirm Password" required><span id="passwordConfirmMessage"><br/></span><br class='bigbr'/>
        <input type="submit" value="Sign Up" class="submitButton"/>
        <span id='statusMessage'></span>
    </form>
    <span id="statusMessage"></span>
</div>
</body>