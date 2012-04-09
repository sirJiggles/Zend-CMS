/* Author: Gareth Fuller

*/

$(document).ready(function () {

    // Sort out the news items height (if they are on the page)
    if ($("#news-articles").length){
        var largestNewsItem = 0;
        var newsItems = $('.news-item-inner');
        /*
         * For each news item we will work out which one is that largest
         * and using that value set them both to be the largest -60
         * because of the negatice top value in the styles (bg image issue)
         */
        for( var i = 0; i <=  newsItems.length; i ++){
            var currentHeight = $(newsItems[i]).height();
            if (currentHeight >= largestNewsItem){
                largestNewsItem = currentHeight;
            }
        }
        $('.news-item-inner').css('height', (largestNewsItem -60) );
    }

    // Paralax
    $('body').parallax({
        'elements': [
            {
            'selector': '#global-wrapper',
            'properties': {
                'x': {
                'background-position-x': {
                    'initial': 0,
                    'multiplier': 0.005,
                    'invert': true
                }
                }
            }
            },
            {
            'selector': '#candle-wrapper',
            'properties': {
                'x': {
                'left': {
                    'initial': -110,
                    'multiplier': 0.09,
                    'unit': 'px'
                }
                }
            }
            }
        ]
    });
    
    
    // Candle js code
    var flashvars = {},
    params = {wmode:"transparent"},
    attributes = {};

    swfobject.embedSWF("/flash/candle.swf", "flashDiv", "500", "360", "9.0.0","/swf/expressInstall.swf", flashvars, params, attributes);
    
    
    // Tooltips on main nav functionality
    $('#home').tipsy({gravity: 'n'});
    $('#events').tipsy({gravity: 'n'});
    $('#pics').tipsy({gravity: 'n'});
    $('#contact').tipsy({gravity: 'n'});
    $('#forum').tipsy({gravity: 'n'});
    $('#rules').tipsy({gravity: 'n'});
    $('#world').tipsy({gravity: 'n'});
    
    // Slide show functionality
    if ($("#slides").length){
        $("#slides").slides({
            preload: true,
            preloadImage: '/img/icons/ajax-loader.gif',
            play: 5000,
            pause: 2500,
            slideSpeed: 600,
            hoverPause: true,
            generatePagination : false
        });
    }
    
    // Image Gallery
    if ($("#gallery-wrapper").length){
        // We only want these styles applied when javascript is enabled
        $('div.navigation').css({'width' : '348px', 'float' : 'left'});
        $('div.content').css('display', 'block');

        // Initially set opacity on thumbs and add
        // additional styling for hover effect on thumbs
        var onMouseOutOpacity = 0.67;
        $('#thumbs ul.thumbs li').opacityrollover({
                mouseOutOpacity:   onMouseOutOpacity,
                mouseOverOpacity:  1.0,
                fadeSpeed:         'fast',
                exemptionSelector: '.selected'
        });

        // Initialize Advanced Galleriffic Gallery
        var gallery = $('#thumbs').galleriffic({
                delay:                     2500,
                numThumbs:                 4,
                preloadAhead:              10,
                enableTopPager:            false,
                enableBottomPager:         false,
                maxPagesToShow:            7,
                imageContainerSel:         '#slideshow',
                controlsContainerSel:      '#controls',
                captionContainerSel:       '#caption',
                loadingContainerSel:       '#loading',
                renderSSControls:          true,
                renderNavControls:         true,
                playLinkText:              'Play Slideshow',
                pauseLinkText:             'Pause Slideshow',
                prevLinkText:              '&lsaquo; Previous Photo',
                nextLinkText:              'Next Photo &rsaquo;',
                nextPageLinkText:          'Next &rsaquo;',
                prevPageLinkText:          '&lsaquo; Prev',
                enableHistory:             false,
                autoStart:                 false,
                syncTransitions:           true,
                defaultTransitionDuration: 900,
                onSlideChange:             function(prevIndex, nextIndex) {
                        // 'this' refers to the gallery, which is an extension of $('#thumbs')
                        this.find('ul.thumbs').children()
                                .eq(prevIndex).fadeTo('fast', onMouseOutOpacity).end()
                                .eq(nextIndex).fadeTo('fast', 1.0);
                },
                onPageTransitionOut:       function(callback) {
                        this.fadeTo('fast', 0.0, callback);
                },
                onPageTransitionIn:        function() {
                        this.fadeTo('fast', 1.0);
                }
        });
        
        /**************** Event handlers for custom next / prev page links **********************/

        $('a.prevPage').click(function(e) {
                e.preventDefault();
                gallery.previousPage();
        });

        $('a.nextPage').click(function(e) {
                e.preventDefault();
                gallery.nextPage();
                
        });


    }
    
    
    // Sort out the page heignt etc (this is not ideal but it works :( )
    var contentHeight = 0;
    if ($("#gallery-wrapper").length){
        contentHeight = 1180;
    }else{
        contentHeight = ($('#page-content').height() + 60);
    }

    if (contentHeight >=  961){
        // The plus value is to compensate for the footer scroll swirl shizzle
        var difference = contentHeight - 961;
        $('#page-content').css('height', difference);
    }else{
        $('#page-content').css('height', 0);
    }
})



















