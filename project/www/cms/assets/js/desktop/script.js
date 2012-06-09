/* 
 * Author: Gareth Fuller
 * 
 */

// Function to indent the pages
function indentPage(object, event){
    // if there is a page above this one o{n the same level
    var previousPage = $(object).parent().parent().parent().prev()[0];
    if (typeof previousPage !== 'undefined'){
        var parent = $(object).parent().parent().parent();
        
        // Check of the previous page contains a sub ul
        var ulCheck = $(previousPage).find('ul.sortable');
        
        if (typeof ulCheck[0] === 'undefined'){
            $(previousPage).append('<ul class="sortable"></ul>');
        }
        $(previousPage).find('> ul.sortable').append('<li class="page-item">'+parent.html()+'</li>');
        
        // remove the old element
        parent.remove();
        saveStructure();
        settupPages();
    }
   
}

// Function to outdent the pages
function outdentPage(object, event){
    var containerPage = $(object).parent().parent().parent().parent().parent();

    if (typeof containerPage !== 'undefined' && $(containerPage).hasClass('page-item')){
        var wrapper = $(object).parent().parent().parent();
        // add this item after current wrapping page
        $('<li class="page-item">'+$(wrapper).html()+'</li>').insertAfter($(containerPage));
        // Remove this item from current page
        $(wrapper).remove();
        saveStructure();
        settupPages();

    }
}

// Function for saving the page structure (at the moment we only go 6 levels deep)
function saveStructure(){

    var structure = '';
    var mainPages = $('#pages > li.page-item');
    
    // Level one check 
    for(var i = 0; i < mainPages.length; i++){
        var mainPageId = $(mainPages[i]).find('.item-wrapper').attr('id');
        structure = structure +'0-'+mainPageId+':';
        var pagesLvlTwo = $(mainPages[i]).children().find('> .page-item');
        
        // Level Two check
        for (var j = 0; j < pagesLvlTwo.length; j++){
            var pageLvlTwoId = $(pagesLvlTwo[j]).find('.item-wrapper').attr('id');
            structure = structure +'1-'+pageLvlTwoId+':';
            var pagesLvlThree = $(pagesLvlTwo[j]).children().find('> .page-item');
            
            // Level Three check
            for (var k = 0; k < pagesLvlThree.length; k++){
                var pageLvlThreeId = $(pagesLvlThree[k]).find('.item-wrapper').attr('id');
                structure = structure +'2-'+pageLvlThreeId+':';
                var pagesLvlFour = $(pagesLvlThree[k]).children().find('> .page-item');
                
                // Level Four check
                for (var l = 0; l < pagesLvlFour.length; l++){
                    var pageLvlFourId = $(pagesLvlFour[l]).find('.item-wrapper').attr('id');
                    structure = structure +'3-'+pageLvlFourId+':';
                    var pagesLvlFive = $(pagesLvlFour[l]).children().find('> .page-item');
                    
                    // Level Five check
                    for (var m = 0; m < pagesLvlFive.length; m++){
                        var pageLvlFiveId = $(pagesLvlFive[m]).find('.item-wrapper').attr('id');
                        structure = structure +'4-'+pageLvlFiveId+':';
                        var pagesLvlSix = $(pagesLvlFive[m]).children().find('> .page-item');
                        
                        // Level Five check
                        for (var n = 0; n < pagesLvlSix.length; n++){
                            var pageLvlSixId = $(pagesLvlSix[n]).find('.item-wrapper').attr('id');
                            structure = structure +'5-'+pageLvlSixId+':';
                            
                        } // End pages lvl 6
                    } // End pages lvl 5
                } // End pages lvl 4
            }// End pages lvl three
        }// End pages lvl two
    } // End top lvl pages
    
    // Make an ajax request to the system saving the page structure
    
    // First get the domain including the protocol
    var url = window.location.href;
    url = url.split('/');
    url = url[0] + '//' + url[2];
    
    // make the request
    $.ajax({
        url: url+'/cms/structure?structure='+structure,
        statusCode: {
            404: function() {
            alert("unable to save to API");
            }
        }
    }).done(function() { 
        // complete
    });
}

function settupPages(){
    $('#pages').sortable({
                        opacity: 0.6,
                        update: function(event, ui) { saveStructure(); }
                        });
    $('#pages ul.sortable').sortable({
                        opacity: 0.6,
                        update: function(event, ui) { saveStructure(); }
                        });
    
     $('a.indent').click(function(e){
        e.preventDefault();
        indentPage(this, e);
    })
        $('a.outdent').click(function(e){
        e.preventDefault();
        outdentPage(this, e);
    })
    
    // Controlls to toggle list view and full view of pages
    $('.content-button-toggle').unbind("click");
    $('.content-button-toggle').click(function(e){
        e.preventDefault();
        $(this).parent().parent().find('.content-buttons').toggle('slow', function() {});

    })
}

$(document).ready(function () {

    if ($('#flash-mssg-container').length > 0){
        // Remove flash messenger after a period of time
        setInterval(fadeFlashMsg,5000);
        
    }

    $.mobile.ajaxEnabled = false;
    
    // Javascript for the sortable pages
    if ($('#pages').length > 0){
        
        settupPages();
        $('.content-buttons').toggle('fast', function() {});

    }
   
})


/*
 * function for fading all the flash msg's that show up
 */
function fadeFlashMsg(){
     $('#flash-mssg-container').fadeOut('slow', function() {});
}





    













