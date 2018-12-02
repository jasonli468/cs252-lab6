$(document).ready(function(){
    $('#loginForm').submit(function(){
        $('#statusMessage').html('Loading...');
        $('#statusMessage').attr('class', '');

        // Send the password and email to be processed and update the page based on the result, encodeURIComponent ensures special characters are passed properly
        let password = $('#passwordInput').val();
        let email = $('#emailInput').val();
        $.post("processlogin.php", 'password=' + encodeURIComponent(password) + '&email=' + encodeURIComponent(email), function(data){
            if(data.status === "Success")
            {
                window.location.href = "index.php";
            }
            else
            {
                $('#statusMessage').html(data.status);
                $('#statusMessage').attr('class', 'error');
            }
        });

        // Return false so the page doesn't refresh
        return false;
    })
})