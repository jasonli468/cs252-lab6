<?php
    // Add a place to the blacklist of the user currently signed in
    session_save_path('/home/campus/li2384/www/tmp');
    session_start();
    $userID = $_SESSION['userID'];
    $placeID = $_POST['placeID'];
    $placeName = $_POST['placeName'];

    if(isset($userID) && isset($placeID) && isset($placeName))
    {
        // Connect to the database and insert the place into the user's blacklist
        include 'dbconnect.php';
        $query = mysqli_prepare($con, "INSERT INTO Blacklist(User_ID, Place_ID, Name) VALUES  (?, ?, ?)");
        mysqli_stmt_bind_param($query, "iss", $userID, $placeID, $placeName);
        mysqli_stmt_execute($query);

        // If there is an affected row, that means the place was added successfuly
        if(mysqli_stmt_affected_rows($query) === 1)
        {
            $response_array['status'] =  "Success";
        }
        // If the place wasn't added, return error
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