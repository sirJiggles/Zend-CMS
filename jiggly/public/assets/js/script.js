/* 
 * Author: Gareth Fuller
 * 
 */
$(document).ready(function () {
    
    if ($('.ui-state-highlight').length > 0){
        // Remove flash messenger after a period of time
        setInterval(fadeFlashMsg,2000);
        
    }
    
    // Turn items into buttons!
    $( ".button").button();
    
    
    // Js for dialog box's on manage users page
    if ($('.dialoge_remove_user').length > 0){
        
        $('.dialoge_remove_user').dialog({
            autoOpen: false,
            width: 600,
            buttons: {
                "Ok": function() { 
                    var currentDialog = $(this).attr('id');
                    currentDialog = currentDialog.replace('dialog_box_user_', '');
                    window.location.replace("/user/remove/id/"+currentDialog);
                }, 
                "Cancel": function() { 
                    $(this).dialog("close"); 
                } 
            }
        });
        
        $('.dialoge_remove_user_link').click(function(){
            var currentDialog = $(this).attr('id');
            $('#dialog_box_user_'+currentDialog).dialog('open');  
            return false;
        });

    }
    
   
})

/*
 * function for fading all the flash msg's that show up
 */
function fadeFlashMsg(){
     $('.ui-state-highlight').fadeOut('slow', function() {});
}





    













