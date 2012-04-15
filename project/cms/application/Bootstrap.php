<?php
/*
 * This is the main Bootstrap file for out application, here we run
 * our initiation of our roots and the access plugin that handles
 * the ACL in the application.
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Bootstraps
 */

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    
    public function _initRoutes(){
        
       
 
        //Get instace of front controller.
        $frontController = Zend_Controller_Front::getInstance();
        //$frontController->setBaseUrl('/cms');
        
        
        //Set up the router.
        $router = $frontController->getRouter();
        
       
        //Create a new static route. it is static because no pattern 
        //matching is required to identify the param location (1st argument)
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
    
    public function _initAcl(){
        // Create a new insance of the acl config (to load the config settings)
        new Access_Config;
        // Register the ACL plugin
        $frontController = Zend_Controller_Front::getInstance();
        $frontController->registerPlugin(new Access_Plugin());
        
        
    }
    
    /*
     * Can only use the config vars on items that are not going to be unit 
     * tested as unit tests cant use registery values
     */
    protected function _initConfig()
    {
        $config = new Zend_Config($this->getOptions(), true);
        Zend_Registry::set('config', $config);
        return $config;
    }
    
}
