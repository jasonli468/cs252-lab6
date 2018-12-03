<?php
    $email = $_POST['email'];
    $token = $_POST['token'];
    $salt = $_POST['salt'];
    $password = $_POST['password'];

    if(isset($email) && isset($token) && isset($password) && isset($salt))
    {
        include 'dbconnect.php';
        $passwordHash = hash('sha256', $salt . $password);
        $query = mysqli_prepare($con, "UPDATE Users SET Password_Hash = ?, Password_Reset_Token = NULL, Token_Expiration = NULL WHERE Email = ? AND Password_Reset_Token = ? AND Token_Expiration > NOW()");
        mysqli_stmt_bind_param($query, "sss", $passwordHash, $email, $token);
        mysqli_stmt_execute($query);
        if(mysqli_stmt_affected_rows($query) === 1)
        {
            $response_array['status'] = 'Success';
            mail($email, "Hungry but Indecisive Boiler Password Has Been Reset", "This email is to inform you that your password has been reset. If you did not do this, please reply to this email IMMEDIATELY");
        }
        // If no rows updated, assume token does not exist for given email
        else
        {
            $response_array['status'] = 'Error: Token does not exist or has expired';
        }
        mysqli_stmt_close($query);
        mysqli_close($con);
    }
    else
    {
        $response_array['status'] = 'Error - Invalid Request';
    }
    header('Content-type: application/json');
    echo json_encode($response_array);
?>