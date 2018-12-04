<?php
    // Edit a preset for to the user currently signed in
    session_save_path('/home/campus/li2384/www/tmp');
    session_start();
    $userID = $_SESSION['userID'];
    $lat = $_POST['lat'];
    $lng = $_POST['lng'];
    $radius = $_POST['radius'];
    $minPrice = $_POST['minPrice'];
    $maxPrice = $_POST['maxPrice'];
    $open = $_POST['open'];
    $name = $_POST['name'];

    if(isset($userID) && isset($lat) && isset($lng) && isset($radius) && isset($minPrice) && isset($maxPrice) && isset($open))
    {
        // Connect to the database and update the preset
        include 'dbconnect.php';
        $open = $open === 'true' ? 1 : 0;
        $query = mysqli_prepare($con, "UPDATE Presets SET Latitude = ?, Longitude = ?, Distance = ?, Min_Price = ?, Max_Price = ?, Open = ?) WHERE User_ID = ? AND Nickname = ?");
        mysqli_stmt_bind_param($query, "dddiii", $lat, $lng, $radius, $minPrice, $maxPrice, $open, $userID, $name);
        mysqli_stmt_execute($query);

        // If there is an affected row, that means the preset was updated successfuly
        if(mysqli_stmt_affected_rows($query) === 1)
        {
            $response_array['status'] =  "Success";
        }
        // If the preset wasn't added, return error
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