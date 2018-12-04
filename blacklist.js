$(document).ready(function(){
    $("input[type='button']").click(function(){
        // Button's parent div will always have it's ID as the place ID
        let placeID = $(this).parent('div').attr("id");
        $(this).val("Deleting...");

        // Delete the place from the blacklist and remove it from the HTML DOM
        $.post('api/removefromblacklist.php', 'placeID=' + placeID, function(data){
            if(data.status === "Success")
                $('#' + placeID).remove();
            else
                $(this).value(data.status);
        })
    })
});