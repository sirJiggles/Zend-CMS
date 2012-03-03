/* 
 * Author: Gareth Fuller
 * 
 */
$(document).ready(function () {
    
    if ($('#flash-message').length > 0){
        // Remove flash messenger after a period of time
        setInterval(fadeFlashMsg,2000);
        
    }
    
    
    // Js for dialog box's on manage users page
    if ($('.dialoge_remove_user').length > 0){
        
        
        
        $('.dialoge_remove_user').dialog({
                autoOpen: false,
                width: 600,
                buttons: {
                        "Ok": function() { 
                            $(this).dialog("close");
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

function fadeFlashMsg(){
     $('#flash-message').fadeOut('slow', function() {});
}





    













