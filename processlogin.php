<?php
    $email = $_POST['email'];
    $password = $_POST['password'];

    if(isset($email) && isset($password))
    {
        // Connect to the database and attempt to get information for the selected email
        include 'dbconnect.php';
        $query = mysqli_prepare($con, "SELECT * FROM Users WHERE Email=?");
        mysqli_stmt_bind_param($query, "s", $email);
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        mysqli_stmt_free_result($query);
        
        // If a row is found, the email exists, so attempt to log in
        if($row = mysqli_fetch_assoc($result))
        {
            // Get the salt and translate the password into the proper hash, and compare with the stored hash
            $passwordHash = hash('sha256', $row['Salt'] . $password);
            if($passwordHash === $row['Password_Hash'])
            {
                // Credentials correct, return success, start a session, and store all relevant variables
                $response_array['status'] = 'Success';
                session_save_path('/home/campus/li2384/www//tmp');
                session_start();
                $_SESSION['userID'] = $row['User_ID'];
                $_SESSION['distance'] = $row['Distance'];
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
        mysqli_free_result($result);
        mysqli_stmt_close($query);
        mysqli_close($con);
    }
    else
        $response_array['status'] = 'Error - Email or Password empty';

    // Return result
    header('Content-type: application/json');
    echo json_encode($response_array);
?>