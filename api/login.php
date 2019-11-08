<?php
    $email = $_POST['email'];
    $password = $_POST['password'];
    if(isset($email) && isset($password))
    {
        // Connect to the database and attempt to get information for the selected email
        include 'dbconnect.php';
        $query = mysqli_prepare($con, "SELECT User_ID, Password_Hash, Salt FROM Users WHERE Email=?");
        mysqli_stmt_bind_param($query, "s", $email);
        mysqli_stmt_execute($query);
        mysqli_stmt_store_result($query);
        
        // If a row is found, the email exists, so attempt to log in
        if(mysqli_stmt_num_rows($query))
        {
            // Get the salt and translate the password into the proper hash, and compare with the stored hash
            mysqli_stmt_bind_result($query, $userID, $hash, $salt);
            mysqli_stmt_fetch($query);
            $passwordHash = hash('sha256', $salt . $password);
            if($passwordHash == $hash)
            {
                // Credentials correct, return success, start a session, and store all relevant variables
                $response_array['status'] = 'Success';
                session_save_path('/home/campus/li2384/www/tmp');
                session_start();
                $_SESSION['userID'] = $userID;
                
                // If remember me set, generate new login token
                if($_POST['remember'])
                {
                    $token = bin2hex(mcrypt_create_iv(32));
                    $tokenQuery = mysqli_prepare($con, "INSERT INTO Login_Tokens(User_ID, Token_Hash, Expiration_Date) VALUES (?, ?, ADDDATE(NOW(), 30))");
                    mysqli_stmt_bind_param($tokenQuery, "is", $userID, hash('sha256', $token));
                    mysqli_stmt_execute($tokenQuery);
                    mysqli_stmt_close($tokenQuery);
                    setcookie('login', $userID . ',' . $token, time() + (60 * 60 * 24 * 30), "", "", true, true);
                }
            }
            else
            {
                $response_array['status'] = 'Email and Password combination not found';
            }
        }
        // If no row found, then the email does not exist, return error
        else
        {
            $response_array['status'] = "Error: Email does not exist";
        }
        
        // Free resources
        mysqli_free_result($query);
        mysqli_stmt_close($query);
        mysqli_close($con);
    }
    else
        $response_array['status'] = 'Error - Email or Password empty';

    // Return result
    header('Content-type: application/json');
    echo json_encode($response_array);
?>