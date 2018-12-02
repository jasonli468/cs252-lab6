<?php
    // Create a new user with the given email and password
    $email = $_POST['email'];
    $password = $_POST['password'];

    if(isset($email) && isset($password))
    {
        // Connect to the database, generate the password salt and hash, and insert it into the database
        include 'dbconnect.php';
        $salt = bin2hex(mcrypt_create_iv(32));
        $passwordHash = hash('sha256', $salt . $password);
        $query = mysqli_prepare($con, "INSERT INTO Users(Email, Password_Hash, Salt) VALUES  (?, ?, ?)");
        mysqli_stmt_bind_param($query, "sss", $email, $passwordHash, $salt);
        mysqli_stmt_execute($query);

        // If there is an affected row, that means the account was created successfully.
        if(mysqli_stmt_affected_rows($query) === 1)
        {
            $response_array['status'] =  "Success";
            session_save_path('/home/campus/li2384/www//tmp');
            session_start();
            $_SESSION['userID'] = mysqli_insert_id($con);
        }
        // If an account wasn't created, assume the email already exists and return error
        else
        {
            $response_array['status'] = "Error: Email already exists";
        }

        // Free resources
        mysqli_stmt_close($query);
        mysqli_close($con);
    }
    else
        $response_array['status'] = 'Error - Email or Password empty';

    // Return result
    header('Content-type: application/json');
    echo json_encode($response_array);
?>