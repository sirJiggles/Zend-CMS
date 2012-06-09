/* 
 * Author: Gareth Fuller
 * 
 */

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
    
    $('a.indent').click(function(e){
        indentPage(this, e);
    })
        $('a.outdent').click(function(e){
        outdentPage(this, e);
    })
}

// Function to outdent the pages
function outdentPage(object, event){
    var containerPage = $(object).parent().parent().parent().parent();

    if (typeof containerPage !== 'undefined' && $(containerPage).hasClass('page-item')){
        var wrapper = $(object).parent().parent();
        // add this item after current wrapping page
        $('<li class="page-item">'+$(wrapper).html()+'</li>').insertAfter($(containerPage));
        // Remove this item from current page
        $(wrapper).remove();
        // re-settup the sortable event
        $('#pages ul.sortable').sortable({opacity: 0.6});

        $('a.indent').click(function(e){
            indentPage(this, e);
        })
            $('a.outdent').click(function(e){
            outdentPage(this, e);
        })
    }
}

$(document).ready(function () {

    if ($('#flash-mssg-container').length > 0){
        // Remove flash messenger after a period of time
        setInterval(fadeFlashMsg,5000);
        
    }

    $.mobile.ajaxEnabled = false;
    
    // Javascript for the sortable pages
    if ($('#pages').length > 0){
        
        // Handle the indent page action
        $('a.indent').click(function(e){
            e.preventDefault();
            indentPage(this, e);
        })
        
        // Handle the outdent page action
        $('a.outdent').click(function(e){
            e.preventDefault();
            outdentPage(this, e);
        })
        
        
        $('#pages').sortable({opacity: 0.6});
        $('#pages ul.sortable').sortable({opacity: 0.6});
        
        // Controlls to toggle list view and full view of pages
        $('#page-alt-view').click(function(e){
            e.preventDefault();
            $('.content-buttons').toggle('slow', function() {
            });
                
        })
       
    }
   
})


/*
 * function for fading all the flash msg's that show up
 */
function fadeFlashMsg(){
     $('#flash-mssg-container').fadeOut('slow', function() {});
}





    













