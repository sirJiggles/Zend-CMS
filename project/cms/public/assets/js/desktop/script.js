/* 
 * Author: Gareth Fuller
 * 
 */
var timer = null;
var isIntervalSet = false;
var totalWidthContentBar = 0;

// Function to indent the pages
function indentPage(object, event){
    // if there is a page above this one o{n the same level
    var previousPage = $(object).parent().parent().prev()[0];
    if (typeof previousPage !== 'undefined'){
        var parent = $(object).parent().parent();
        
        // Check of the previous page contains a sub ul
        var ulCheck = $(previousPage).find('ul.sortable');
        
        if (typeof ulCheck[0] === 'undefined'){
            $(previousPage).append('<ul class="sortable"></ul>');
        }
        
        $(previousPage).find('ul.sortable').append('<li clas="page-item">'+parent.html()+'</li>');
        // remove the old element
        parent.remove();
    }
    // Apply sortable functionality
    $('#pages ul.sortable').sortable({opacity: 0.6});
}

// Function to outdent the pages
function outdentPage(object, event){
    var containerPage = $(object).parent().parent().parent().parent();
    var wrapper = $(object).parent().parent();
    // add this item after current wrapping page
    $('<li class="page-item">'+$(wrapper).html()+'</li>').insertAfter($(containerPage));
    // Remove this item from current page
    $(wrapper).remove();
    // re-settup the sortable event
    $('#pages ul.sortable').sortable({opacity: 0.6});
}

$(document).ready(function () {

    if ($('#flash-mssg-container').length > 0){
        // Remove flash messenger after a period of time
        setInterval(fadeFlashMsg,5000);
        
    }

    //$.mobile.ajaxEnabled = false;
    
    // Javascript for the sortable pages
    if ($('#pages').length > 0){
        
        // Handle the indent page action
        $('a.indent').click(function(e){
            e.preventDefault();
            indentPage(this, e);
            
            // add actions for buttons again
            $('a.indent').click(function(e){
                indentPage(this, e);
            })
             $('a.outdent').click(function(e){
                outdentPage(this, e);
            })
            
        })
        
        // Handle the outdent page action
        $('a.outdent').click(function(e){
            e.preventDefault();
            outdentPage(this, e);
            // add actions for buttons again
            $('a.indent').click(function(e){
                indentPage(this, e);
            })
             $('a.outdent').click(function(e){
                outdentPage(this, e);
            })
            
        })
        
        
        $('#pages').sortable({opacity: 0.6});
        $('#pages ul.sortable').sortable({opacity: 0.6});
       
    }
   
   
   /*
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

   }*/
   
})


/*
 * function for fading all the flash msg's that show up
 */
function fadeFlashMsg(){
     $('#flash-mssg-container').fadeOut('slow', function() {});
}





    













