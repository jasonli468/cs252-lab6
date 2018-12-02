<?php
    $email = $_GET['email'];
    if(isset($email))
    {
        include 'dbconnect.php';
        $query = mysqli_prepare($con, "SELECT * FROM Users WHERE Email = ?");
        mysqli_stmt_bind_param($query, "s", $email);
        mysqli_stmt_execute($query);
        mysqli_stmt_store_result($query);
        $response_array['status'] = 'Success';
        $response_array['existingEmail'] = mysqli_stmt_num_rows($query);
        mysqli_stmt_free_result($query);
        mysqli_stmt_close($query);
        mysqli_close($con);
    }
    else
        $response_array['status'] = 'Error - Email cannot be empty';
    header('Content-type: application/json');
    echo json_encode($response_array);
?>