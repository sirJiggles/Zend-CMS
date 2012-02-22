<?php

/*
 * This is the class that deals with user authentification
 * and redirecting 
 * 
 */

class Auth_AuthPlugin extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $loginController = 'user';
        $loginAction     = 'login';

        $auth = Zend_Auth::getInstance();

        // If user is not logged in and is not requesting login page
        // - redirect to login page.
        if (!$auth->hasIdentity()
                && $request->getControllerName() != $loginController
                && $request->getActionName()     != $loginAction) {

            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
            $redirector->gotoSimpleAndExit($loginAction, $loginController);
        }

        // User is logged in or on login page.

        if ($auth->hasIdentity()) {
            // Is logged in
            // Let's check the credential
            $registry = Zend_Registry::getInstance();
            $acl = $registry->get('acl');
            $identity = $auth->getIdentity();
            // role is a column in the user table (database)
            $isAllowed = $acl->isAllowed($identity->role,
                                         $request->getControllerName(),
                                         $request->getActionName());
            if (!$isAllowed) {
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
                $redirector->gotoUrlAndExit('/');
            }
        }
    }
}