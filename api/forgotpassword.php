<?php
	$email = $_POST['email'];
	if(isset($email))
	{
		include 'dbconnect.php';
        $token = bin2hex(mcrypt_create_iv(32));
        $query = mysqli_prepare($con, "UPDATE Users SET Password_Reset_Token = ?, Token_Expiration = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE Email=?");
        mysqli_stmt_bind_param($query, "ss", $token, $email);
        mysqli_stmt_execute($query);
        if(mysqli_stmt_affected_rows($query) === 1)
        {
            $emailBody = "A password reset has been requested for your account. If you did not request this, please ignore this email. " .
                "If you did request this, please follow the link below to reset your password, which will expire in one hour:\r\n" .
                "https://web.ics.purdue.edu/~li2384/cs252-lab6/resetpassword.php?email=" . $email . "&token=" . $token;
            mail($email, "Hungry but Indecisive Boiler Password Reset Request", $emailBody);
            $response_array['status'] = 'Success';
        }
        else
        {
            $response_array['status'] = 'Error: No account exists for that email';
        }
        mysqli_stmt_close($query);
        mysqli_close($con);
	}
	else
	{
        $response_array['status'] = 'Error - Email empty';
	}

	header('Content-type: application/json');
	echo json_encode($response_array);
?>