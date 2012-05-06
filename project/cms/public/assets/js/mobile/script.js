/* 
 * Author: Gareth Fuller
 * 
 */
$(document).ready(function () {

    if ($('#flash-mssg-container').length > 0){
        // Remove flash messenger after a period of time
        setInterval(fadeFlashMsg,5000);
        
    }
    $.mobile.ajaxEnabled = false;

    // Main navigation form submit
    $('select[name="nav"]').change(function(){
        $('#main-nav-form').submit();
    });
   
})

/*
 * function for fading all the flash msg's that show up
 */
function fadeFlashMsg(){
     $('#flash-mssg-container').fadeOut('slow', function() {});
}





    













