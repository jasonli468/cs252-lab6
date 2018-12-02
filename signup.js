var passwordValid = false;
var emailValid = false;
var password = '';
var passwordConfirm = '';
var email = '';
var emailConfirm = '';

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

    // Lots of if statements for error checking
    $('#emailInput').on('input', function(){
        emailValid = false;
        email = $.trim($('#emailInput').val());
        $('#emailInput').val(email);
        if(email)
        {
            // Check if its in the form {string}@{string}.{string lengt 2-4} with appropriate characters
            let emailRegex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            if(emailRegex.test(email))
            {
                emailValid = true;
                $('#emailMessage').html('Checking availability...');
                $('#emailMessage').attr('class', '');

                $.get("checkemail.php", 'email=' + email, function(data){
                    if(data.status === "Success")
                    {
                        if(data.existingEmail === 0)
                        {
                            $('#emailMessage').html('Email available');
                            $('#emailMessage').attr('class', 'success');
                        }
                        else
                        {
                            $('#emailMessage').html('Email taken');
                            $('#emailMessage').attr('class', 'error');
                        }
                    }
                    else
                    {
                        $('#emailMessage').html(data.status)
                        $('#emailMessage').attr('class', 'error');
                    }
                })
            }
            else
            {
                $('#emailMessage').html('Not a valid email address');
                $('#emailMessage').attr('class', 'error');
            }

            let emailConfirm = $.trim($('#emailConfirmInput').val());
            if(emailValid && email === emailConfirm)
            {
                $('#emailConfirmMessage').html('Emails match')
                $('#emailConfirmMessage').attr('class', 'success');
            }
            else if(emailConfirm)
            {
                $('#emailConfirmMessage').html('Emails do not match')
                $('#emailConfirmMessage').attr('class', 'error');
            }
            else
            {
                $('#passwordConfirmMessage').html('<br/>')
            }
        }
        else
        {
            $('#emailMessage').html('Email cannot be empty');
            $('#emailMessage').attr('class', 'error');
        }
    })      
    $('#emailConfirmInput').on('input', function(){
        email = $.trim($('#emailInput').val());
        emailConfirm = $.trim($('#emailConfirmInput').val());
        if(email && emailValid && email === emailConfirm)
        {
            $('#emailConfirmMessage').html('Emails match')
            $('#emailConfirmMessage').attr('class', 'success');
        }
        else if(emailConfirm)
        {
            $('#emailConfirmMessage').html('Emails do not match')
            $('#emailConfirmMessage').attr('class', 'error');
        }
        else
        {
            $('#passwordConfirmMessage').html('<br/>')
        }
    })
    
    $('#signUpForm').submit(function(){
        if(emailValid && passwordValid)
        {
            $('#statusMessage').html('Loading...');
            $('#statusMessage').attr('class', '');
            
            if(password === passwordConfirm && email === emailConfirm)
            {
                // Send the password and email to be processed and update the page based on the result, encodeURIComponent ensures special characters are passed properly
                $.post("createuser.php", 'email=' + encodeURIComponent(email) + '&password=' + encodeURIComponent(password), function(data){
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
            }
            else if(email === emailConfirm)
            {
                $('#statusMessage').html("Your passwords must match");
                $('#statusMessage').attr('class', 'error');
            }
            else
            {
                $('#statusMessage').html("Your emails must match");
                $('#statusMessage').attr('class', 'error');
            }
        }

        // Return false so the page doesn't refresh
        return false;
    })
})