<!DOCTYPE html>
<html lang = "en">
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="resetpassword.js"></script>
    
    <title>Reset Password - Hungry but Indicisive Boiler</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
<?php include 'header.php';?>

<div class="container">
    <h2>Reset Password</h2>
    <?php
        $email = $_GET['email'];
        $token = $_GET['token'];
    
        if(isset($email) && isset($token))
        {
            include 'dbconnect.php';
            $query = mysqli_prepare($con, "SELECT Salt FROM Users WHERE Email = ? AND Password_Reset_Token = ? AND Token_Expiration > NOW()");
            mysqli_stmt_bind_param($query, "ss", $email, $token);
            mysqli_stmt_execute($query);
            $result = mysqli_stmt_get_result($query);
            mysqli_stmt_free_result($query);
            if(mysqli_num_rows($result) === 1)
            {
                echo '
                    <form id="passwordResetForm">
                        <label>New Password:</label>
                        <input id="passwordInput" type="password" placeholder="Enter New Password" required><span id="passwordMessage"><br></span><br>
                        <label>Confirm New Password:</label>
                        <input id="passwordConfirmInput" type="password" placeholder="Confirm New Password" required><span id="passwordConfirmMessage"><br></span><br>
                        <input type="submit" value="Submit" class="submitButton"><span id="statusMessage"></span>
                        <input id="token" type="hidden" value=' . $token . ' hidden/>
                        <input id="email" type="hidden" value=' . $email . ' hidden/>
                        <input id="salt" type="hidden" value=' . mysqli_fetch_assoc($result)['Salt'] . ' hidden/>
                    </form>';
            }
            else
            {
                echo 'Token does not exist or has expired';
            }
            mysqli_free_result($result);
            mysqli_stmt_close($query);
            mysqli_close($con);
        }
        else
        {
            exit("Invalid request");
        }
    ?>
    <span id="statusMessage"><br/></span>
</div>

</body>