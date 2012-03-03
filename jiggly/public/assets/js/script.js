/* 
 * Author: Gareth Fuller
 * 
 */
$(document).ready(function () {
    
    if ($('#flash-message').length > 0){
        // Remove flash messenger after a period of time
        setInterval(fadeFlashMsg,2000);
        
    }
   
})

function fadeFlashMsg(){
     $('#flash-message').fadeOut('slow', function() {});
}





    













