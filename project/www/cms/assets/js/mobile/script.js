/* 
 * Author: Gareth Fuller
 * 
 * Mobile javascript file
 * 
 */

$(document).ready(function () {
    
    // Main navigation form submit
    $('select[name="nav"]').change(function(){
        $('#main-nav-form').submit();
    });
    
    $.mobile.hidePageLoadingMsg();

})
