<?php
    // Remove a place from the blacklist of the user currently signed in
    session_save_path('/home/campus/li2384/www/tmp');
    session_start();
    $userID = $_SESSION['userID'];
    $placeID = $_POST['placeID'];

    if(isset($userID) && isset($placeID))
    {
        // Connect to the database and insert the place into the user's blacklist
        include 'dbconnect.php';
        $query = mysqli_prepare($con, "DELETE FROM Blacklist WHERE User_ID = ? AND Place_ID = ?");
        mysqli_stmt_bind_param($query, "is", $userID, $placeID);
        mysqli_stmt_execute($query);

        // If there is an affected row, that means the place was deleted successfuly
        if(mysqli_stmt_affected_rows($query) === 1)
        {
            $response_array['status'] =  "Success";
        }
        // If the place wasn't deleted, return error
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