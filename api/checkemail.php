<?php
    // Check if an email already exists in the database
    $email = $_GET['email'];
    if(isset($email))
    {
        // Connect to the database and run a query for all users with the given email
        include 'dbconnect.php';
        $query = mysqli_prepare($con, "SELECT * FROM Users WHERE Email = ?");
        mysqli_stmt_bind_param($query, "s", $email);
        mysqli_stmt_execute($query);
        mysqli_stmt_store_result($query);

        // Return the number of users with the given email
        $response_array['status'] = 'Success';
        $response_array['existingEmail'] = mysqli_stmt_num_rows($query);

        // Freeing resources
        mysqli_free_result($query);
        mysqli_stmt_close($query);
        mysqli_close($con);
    }
    else
        $response_array['status'] = 'Error - Email cannot be empty';

    // Return results
    header('Content-type: application/json');
    echo json_encode($response_array);
?>