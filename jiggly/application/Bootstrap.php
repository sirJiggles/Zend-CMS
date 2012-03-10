<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
   
    public function _initRoutes(){
 
        //Get instace of front controller.
        $frontController = Zend_Controller_Front::getInstance();
        //Set up the router.
        $router = $frontController->getRouter();
        //Create a new static route. it is static because no pattern matching is required to identify the param location (1st argument)
        $route = new Zend_Controller_Router_Route_Static('login',
                                                          array( 'controller' => 'user',
                                                                 'action' => 'login')
                                                         );
        //Add the route to the router.
        $router->addRoute('login', $route); 
        
        // Logout route
        $route = new Zend_Controller_Router_Route_Static('logout',
                                                          array( 'controller' => 'user',
                                                                 'action' => 'logout')
                                                         );
        //Add the route to the router.
        $router->addRoute('logout', $route); 
 
    }//end function _initRoutes
    
    public function _initAcl()
    {
        // Added to get working on ubuntu machine (must be mod issue but dont have time for this shit)
        //require_once '/home/gareth/Dropbox/Zend-CMS/jiggly/library/acl/Config.php';
        //require_once '/home/gareth/Dropbox/Zend-CMS/jiggly/library/acl/Plugin.php';

        // Create a new insance of the acl config (to load the config settings)
        $acl = new Acl_Config();
        
        // Register the ACL plugin
        $frontController = Zend_Controller_Front::getInstance();
        $frontController->registerPlugin(new Acl_Plugin());
    }

}
