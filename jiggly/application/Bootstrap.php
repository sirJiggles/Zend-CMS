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
                                                          array( 'controller' => 'User',
                                                                 'action' => 'login')
                                                         );
        //Add the route to the router.
        $router->addRoute('login', $route); 
 
    }//end function _initRoutes
   
}
