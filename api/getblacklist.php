<?php
    session_save_path('/home/campus/li2384/www/tmp');
    session_start();
    
    // If logged in, get the list of places a user has decided to ignore, otherwise return error.
    if(isset($_SESSION['userID']))
    {
        // Get list of place IDs from DB. Sort them in order to allow binary search later for faster searching through list
        include 'dbconnect.php';
        $query = mysqli_prepare($con, "SELECT Place_ID FROM Blacklist WHERE User_ID = ? ORDER BY Place_ID ASC");
        mysqli_stmt_bind_param($query, "i", $_SESSION['userID']);
        mysqli_stmt_execute($query);
        $result = mysqli_stmt_get_result($query);
        mysqli_stmt_free_result($query);

        // If any are found, set response status to success and add them all to the response
        if($row = mysqli_fetch_assoc($result))
        {
            $response_array['placeIDs'] = array();
            do
            {
                $response_array['placeIDs'][] = $row['Place_ID'];
            } while($row = mysqli_fetch_assoc($result));

            $response_array['status'] = 'Success';
        }
        else
        {
            $response_array['status'] = 'Empty';
        }
        mysqli_free_result($result);
        mysqli_stmt_close($query);
        mysqli_close($con);
    }
    else
    {
        $response_array['status'] = 'Not logged in';
    }

    header('Content-type: application/json');
    echo json_encode($response_array);
?>