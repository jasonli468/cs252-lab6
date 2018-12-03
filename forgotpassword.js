let sending = false;

$(document).ready(function(){
    $('#emailForm').submit(function(){
        let email = $('#emailInput').val();
        if(email)
        {
            $('#statusMessage').html("Processing...");
            $('#statusMessage').attr('class', '');

            // So the request doesn't get spammed
            if(!sending)
            {
                sending = true;

                // Send the email to be processed and update the page accordingly. encodeURIComponent ensures special characters are sent correctly
                $.post("api/forgotpassword.php", 'email=' + encodeURIComponent(email), function(data){
                    if(data.status === "Success")
                    {
                        $('#statusMessage').html('An email has been sent with instructions to reset your password');
                        $('#statusMessage').attr('class', 'success');
                    }
                    else
                    {
                        $('#statusMessage').html(data.status);
                        $('#statusMessage').attr('class', 'error');
                        sending = false;
                    }
                })
            }
        }
        else
        {
            $('#statusMessage').html("Email cannot be empty");
            $('#statusMessage').attr('class', 'error');
        }
        
        // Return false so the page doesn't refresh
        return false;
    })
})