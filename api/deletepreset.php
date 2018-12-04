<?php
    // Remove a preset of the user currently signed in
    session_save_path('/home/campus/li2384/www/tmp');
    session_start();
    $userID = $_SESSION['userID'];
    $name = $_POST['name'];

    if(isset($userID) && isset($name))
    {
        // Connect to the database and delete the preset
        include 'dbconnect.php';
        $query = mysqli_prepare($con, "DELETE FROM Presets WHERE User_ID = ? AND Nickname = ?");
        mysqli_stmt_bind_param($query, "is", $userID, $name);
        mysqli_stmt_execute($query);

        // If there is an affected row, that means the preset was deleted successfuly
        if(mysqli_stmt_affected_rows($query) === 1)
        {
            $response_array['status'] =  "Success";
        }
        // If the preset wasn't deleted, return error
        else
        {
            $response_array['status'] = "Error: " . mysqli_stmt_error($query);
        }

        // Free resources
        mysqli_stmt_close($query);
        mysqli_close($con);
    }
    else
        $response_array['status'] = 'Invalid Request';

    // Return result
    header('Content-type: application/json');
    echo json_encode($response_array);
?>