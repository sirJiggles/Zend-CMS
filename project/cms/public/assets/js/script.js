/* 
 * Author: Gareth Fuller
 * 
 */
$(document).ready(function () {

    if ($('#flash-mssg-container').length > 0){
        // Remove flash messenger after a period of time
        setInterval(fadeFlashMsg,2000);
        
    }
    //$.mobile.page.prototype.options.addBackBtn = true;
    
   
})

/*
 * function for fading all the flash msg's that show up
 */
function fadeFlashMsg(){
     $('#flash-mssg-container').fadeOut('slow', function() {});
}





    













