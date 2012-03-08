<?php

/*
 * This is the class that deals with user authentification
 * and redirecting 
 * 
 */

class Acl_Plugin extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $auth = Zend_Auth::getInstance();
        $ident = $auth->getIdentity();
        
        // If user is not logged in and is not requesting login page
        $currentAction = $request->getActionName();
        $currentController = $request->getControllerName();
        
        // Create a concat of the login path to cmpare against
        $controllerActionConcat = $currentController.'/'.$currentAction;
        
        //Create an arry of allowed URL's for access without loggin
        $noLoginRequiredLinks = array('user/login',
                                      'user/forgot-password',
                                      'user/activate-password');
            
        if ((!isset($ident)) && (!in_array($controllerActionConcat, $noLoginRequiredLinks))){
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
            $redirector->gotoSimpleAndExit('login', 'user');
            return;

        }

        // User is logged in or on login page
        if ($auth->hasIdentity()) {
            // Is logged in
            // Let's check the credential
            $registry = Zend_Registry::getInstance();
            $acl = $registry->get('acl');
            $identity = $auth->getIdentity();
            // role is a column in the user table (database)
            $isAllowed = $acl->isAllowed($identity->role,
                                         $currentController,
                                         $currentAction);
            if (!$isAllowed) {
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
                $redirector->gotoUrlAndExit('/error/not-the-droids');
                return;
            }
        }
    }
}