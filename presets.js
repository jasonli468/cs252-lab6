$(document).ready(function(){
    $("input[type='button']").click(function(){
        if(confirm("Are you sure you want to delete this preset? This is permanent and cannot be undone"))
        {
            // Button's parent form will always have it's ID as the nickname
            let name = $(this).parent('form').attr("id");

            // Update button to show message and disable it to not spam the server
            $(this).val("Deleting...");
            $(this).prop('disabled', true);

            // Delete the preset and remove it from the HTML DOM
            $.post('api/deletepreset.php', 'name=' + name, function(data){
                if(data.status === "Success")
                    $('#' + name).remove();
                else
                {
                    $(this).val(data.status);
                    $(this).prop('disabled', false);
                }
            })
        }
    })
});