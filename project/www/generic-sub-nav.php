<!doctype html>
<!-- paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/ -->
<!--[if lt IE 7]> <html class="no-js ie6 oldie" lang="en"> <![endif]-->
<!--[if IE 7]>    <html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]>    <html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!-- Consider adding an manifest.appcache: h5bp.com/d/Offline -->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->
<head>
  <meta charset="utf-8">

  <!-- Use the .htaccess and remove these lines to avoid edge case issues.
       More info: h5bp.com/b/378 -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

  <title></title>
  <meta name="description" content="">
  <meta name="author" content="">

  <!-- Mobile viewport optimized: j.mp/bplateviewport -->
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- Place favicon.ico and apple-touch-icon.png in the root directory: mathiasbynens.be/notes/touch-icons -->

  <!-- CSS: implied media=all -->
  <!-- CSS concatenated and minified via ant build script-->
  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/tipsy.css">
  
  <!-- end CSS-->

  <!-- More ideas for your <head> here: h5bp.com/d/head-Tips -->
  
  
    <script src="js/mylibs/cufon.js" type="text/javascript"></script>
    <script src="js/mylibs/Old_English_Text_MT_400.font.js" type="text/javascript"></script>
    <!-- Font for title -->
    <script type="text/javascript">
            Cufon.replace('h1');
            Cufon.replace('h2');
            Cufon.replace('h3');
    </script>

  <!-- All JavaScript at the bottom, except for Modernizr / Respond.
       Modernizr enables HTML5 elements & feature detects; Respond is a polyfill for min/max-width CSS3 Media Queries
       For optimal performance, use a custom Modernizr build: www.modernizr.com/download/ -->
  <script src="js/libs/modernizr-2.0.6.min.js"></script>
  
</head>

<body>
    
    <div id="global-wrapper">
        
        <div id="main-wrapper">

            <div id="pagewrap">

                <div id="page-content" role="main">

                    <header id="header">

                        <nav id="main-nav">
                            <a id="home" href="index.html" title="Home"></a>
                            <a class="active" id="events" href="#" title="Events"></a>
                            <a id="pics" href="gallery.php" title="Pictures"></a>
                            <a id="contact" href="#" title="Contact Us"></a>
                            <a id="forum" href="#" title="Forum"></a>
                            <a id="rules" href="#" title="Rules"></a>
                            <a id="world" href="#" title="World"></a>
                        </nav>

                    </header>

                    <div id="content">
                        
                        <h1>Eventyr<br /><span>Pure adventure</span></h1>
                        
                        <!-- Start of the two col wrapper-->
                        <div id="two-col-wrapper">
                        
                            <!-- Start of the sub navigation section -->
                            <section id="sub-nav-section">
                                <h3>Events</h3>
                                <nav id="sub-nav">
                                    <a href="#" class="active">Event one</a>
                                    <a href="#" >Event two</a>
                                    <a href="#" >Event three</a>
                                </nav>
                            </section>
                            
                            <!-- Start of the second col content -->
                            <section id="second-col-content">
                                
                                <h2>Event one</h2>
                                
                                <p>Jowl frankfurter ribeye, biltong turkey sausage chicken shank. 
                                Tri-tip tail leberkase pork chop andouille salami. Beef ribs 
                                hamburger brisket ground round kielbasa andouille. Bresaola 
                                chicken fatback ham pastrami shankle, shank turducken. 
                                Biltong beef frankfurter jowl pork, strip steak drumstick ham hock 
                                shank meatball pastrami sirloin andouille pork loin.</p>
                                
                                <h3>Sub title</h3>

                                <p>Pork drumstick turducken rump, chicken tenderloin chuck 
                                sausage <strong>meatloaf</strong> beef shoulder meatball. Pork strip steak 
                                tongue pig ham. Corned beef bacon prosciutto, fatback tongue 
                                pig beef ribs rump. Ribeye pig short loin jerky tenderloin, filet 
                                mignon swine strip steak leberkase <a href="#">Some link</a> rump prosciutto corned beef.</p>
                                
                                <h4>Sub sub title</h4>
                                
                                <ul>
                                    <li>Item one</li>
                                    <li>Item two</li>
                                    <li>Item three</li>
                                    <li><a href="#">Link Item four</a></li>
                                </ul>
                                
                                
                            </section>
                            
                            
                        </div>
                        <!-- End of the two col wrapper -->
                    </div>
                    <!-- End content -->

                    <footer id="footer">
                            
                    </footer>
                </div>
                <!--end page content div-->
            </div>
            <!--end page wrap -->
            
            <div id="scroll-bottom">
            </div>
        </div>
        <!--end the main wrapper-->

        
        <div id="candle-wrapper">
            <div id="flash-wrapper">
                    <div id="flashDiv">
                        <img src="/img/candle-replace.png" />
                    </div>
            </div>
        </div>
    </div>
    <!--end global wrapper-->

  <!-- JavaScript at the bottom for fast page loading -->

  <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
  <script src="//ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
  <script>window.jQuery || document.write('<script src="js/libs/jquery-1.6.2.min.js"><\/script>')</script>


  <!-- scripts concatenated and minified via ant build script-->
  <script defer src="js/mylibs/tipsy.js"></script>
  <script defer src="js/mylibs/plax.js"></script>
  <script defer src="js/mylibs/swfobject.js"></script>
  <script defer src="js/plugins.js"></script>
  <script defer src="js/script.js"></script>
  <!-- end scripts-->

	
  <!-- Change UA-XXXXX-X to be your site's ID -->
  <script>
    window._gaq = [['_setAccount','UAXXXXXXXX1'],['_trackPageview'],['_trackPageLoadTime']];
    Modernizr.load({
      load: ('https:' == location.protocol ? '//ssl' : '//www') + '.google-analytics.com/ga.js'
    });
  </script>


  <!-- Prompt IE 6 users to install Chrome Frame. Remove this if you want to support IE 6.
       chromium.org/developers/how-tos/chrome-frame-getting-started -->
  <!--[if lt IE 7 ]>
    <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
    <script>window.attachEvent('onload',function(){CFInstall.check({mode:'overlay'})})</script>
  <![endif]-->
  
</body>
</html>
