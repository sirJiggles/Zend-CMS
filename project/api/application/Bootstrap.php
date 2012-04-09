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
    
    protected function _initRestRoute()
    {
        $this->bootstrap('frontController');
        $frontController = Zend_Controller_Front::getInstance();
        $restRoute = new Zend_Rest_Route($frontController);
        $frontController->getRouter()->addRoute('default', $restRoute);

    }
    
    public function _initRestAuth(){
        // Register the RestAuth plugin
        $frontController = Zend_Controller_Front::getInstance();
        $frontController->registerPlugin(new RestAuth_Plugin());
        
        
    }
   

}
