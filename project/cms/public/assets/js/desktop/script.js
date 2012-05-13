/* 
 * Author: Gareth Fuller
 * 
 */
var timer = null;
var isIntervalSet = false;
var totalWidthContentBar = 0;

$(document).ready(function () {

    if ($('#flash-mssg-container').length > 0){
        // Remove flash messenger after a period of time
        setInterval(fadeFlashMsg,5000);
        
    }

    $.mobile.ajaxEnabled = false;
   
   
   // Javascript for the homepage content scrolloing functionality
   if ($('#bottom-container-home').length > 0){
       
       // Get all the li's in the ul and their width to work out the width of the ul
       var liItems = $('#bottom-container-home').find('li');
       
       for(var i = 0; i < liItems.length; i ++){
           // Get the width of the item
           totalWidthContentBar = totalWidthContentBar + ($(liItems[i]).width() + 25);
       }
       $('#bottom-container-home ul').css('width', totalWidthContentBar);
     

        $('#bottom-container-home').mousemove(function(e) {
            if (isIntervalSet) {
                return;
            }
            timer = window.setInterval(function() {
                var centerPoint = ($(window).width() / 2);
                var windowWidth = $(window).width();
                var moveAmount = 0;
                var currentLeft = $('#bottom-container-home ul').css('left');
                currentLeft = parseInt(currentLeft.replace('px', ''));
                if ((e.pageX > (centerPoint + 120)) || (e.pageX < (centerPoint - 120))){
                    if (e.pageX > centerPoint){
                        if ( (windowWidth - currentLeft) <= (totalWidthContentBar  + 40)){
                            moveAmount = currentLeft - 5;
                        }else{
                            moveAmount = currentLeft;
                        }

                    }else{
                        if (currentLeft < 0){
                            moveAmount = currentLeft + 5;
                        }
                    }
                }else{
                   moveAmount =  currentLeft;
                }
                //console.log(windowWidth);
                $('#bottom-container-home ul').css('left', moveAmount);  
            }, 10);
            isIntervalSet = true;
        }).mouseout(function() {
            isIntervalSet = false;
            window.clearTimeout(timer);
            timer = null;
        });

   }
   
})


/*
 * function for fading all the flash msg's that show up
 */
function fadeFlashMsg(){
     $('#flash-mssg-container').fadeOut('slow', function() {});
}





    













