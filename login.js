$(document).ready(function(){
    $('#loginForm').submit(function(){
        $('#statusMessage').html('Loading...');
        $('#statusMessage').attr('class', '');
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
        return false;
    })
})