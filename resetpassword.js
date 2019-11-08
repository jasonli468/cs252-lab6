let password =  '';
let passwordConfirm = '';
let passwordValid = false;
let sending = false;

$(document).ready(function(){
    // Lots of if statements for error checking
    $('#passwordInput').on('input', function(){
        passwordValid = false;
        password = $('#passwordInput').val();
        if(password)
        {
            if(password.length >= 6)
            {
                // Check for any of the following characters: [ ] ! * \ ( )
                let invalidPasswordRegex = /[\[\]!\s*'\(\)]/;
                if(!invalidPasswordRegex.test(password))
                {
                    // Check for any non-alphanumeric character (aka any special character)
                    let alphanumericRegex = /^[A-Za-z0-9]*$/;
                    if(!alphanumericRegex.test(password) || password.length > 10)
                    {
                        $('#passwordMessage').html("Strong password")
                        $('#passwordMessage').attr('class', 'success');
                    }
                    else
                    {
                        $('#passwordMessage').html("Weak password")
                        $('#passwordMessage').attr('class', 'warning');
                    }
                    passwordValid = true;
                }
                else
                {
                    $('#passwordMessage').html("Password cannot contain any spaces or any of the following characters: [ ] ! * \ ( )")
                    $('#passwordMessage').attr('class', 'error');
                }
            }
            else
            {
                $('#passwordMessage').html('Password must be at least 6 characters long')
                $('#passwordMessage').attr('class', 'error');
            }

            if(passwordValid && password === passwordConfirm)
            {
                $('#passwordConfirmMessage').html('Passwords match')
                $('#passwordConfirmMessage').attr('class', 'success');
            }
            else if(passwordConfirm)
            {
                $('#passwordConfirmMessage').html('Passwords do not match')
                $('#passwordConfirmMessage').attr('class', 'error');
            }
            else
            {
                $('#passwordConfirmMessage').html('<br/>')
            }
        }
        else
        {
            $('#passwordMessage').html('Password cannot be empty')
            $('#passwordMessage').attr('class', 'error');
        }
    })
    $('#passwordConfirmInput').on('input', function(){
        password = $('#passwordInput').val();
        passwordConfirm = $('#passwordConfirmInput').val();
        if(password && passwordValid && password === passwordConfirm)
        {
            $('#passwordConfirmMessage').html('Passwords match')
            $('#passwordConfirmMessage').attr('class', 'success');
        }
        else if(passwordConfirm)
        {
            $('#passwordConfirmMessage').html('Passwords do not match')
            $('#passwordConfirmMessage').attr('class', 'error');
        }
        else
        {
            $('#passwordConfirmMessage').html('<br/>')
        }
    })

    $('#passwordResetForm').submit(function(){
        let password = $('#passwordInput').val();
        let passwordConfirm = $('#passwordConfirmInput').val();
        let email = $('#email').val();
        let token = $('#token').val();
        let salt = $('#salt').val();

        if(passwordValid && password === passwordConfirm && !sending)
        {
            sending = true;
            $('#statusMessage').html("Loading...");
            $('#statusMessage').attr('class', '');

            // encodeURIComponent ensures special characters are processed properly
            $.post("api/resetpassword.php", 'email=' + encodeURIComponent(email) + '&token=' + token + '&password=' + encodeURIComponent(password) + '&salt=' + salt, function(data){
                if(data.status = "Success")
                {
                    window.location.href = "login.php";
                }
                else
                {
                    sending = false;
                    $('#statusMessage').html(data.status);
                    $('#statusMessage').attr('class', 'error');
                }
            })
        }

        return false;
    })
})