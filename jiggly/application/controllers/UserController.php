<?php

class UserController extends Zend_Controller_Action
{
    
   

    public function indexAction()
    {
        // action body
        
    }
    
    /*
     * Login action, used for logging users into the CMS
     * This action contains 
     */
    public function loginAction()
    {
        // Get a new instance of the login form an set the prams action and method
        $form = new Application_Form_Login();
        $form->setAction('/login');
        $form->setMethod('post');
        
        // Send the form to the view
        $this->view->loginForm = $form;
        
        // If the user has posted the form
        if ($this->getRequest()->isPost()){
            
            // Check if the form data is valid
            if ($form->isValid($_POST)) {
                
                // Get the database adapter
                $db = $this->_getParam('db');
                
                // Create a new instance of the auth adapter, letting it know how we will treat the creds
                $adapter = new Zend_Auth_Adapter_DbTable(
                    $db,
                    'users',
                    'username',
                    'password',
                    'MD5(CONCAT(?, password_salt))'
                );
                
                // Get the values form the form
                $values = $form->getValues();
                
                // Set the identity and credencial values for the auth adapter 
                $adapter->setIdentity($values['username']);
                $adapter->setCredential($values['password']);
                
                // Get an instance of Zend_Auth
                $auth   = Zend_Auth::getInstance();
                // Check if the values in the adapter are correct (authenticate)
                $result = $auth->authenticate($adapter);
               
                print_r($result);
                
                // If the creds are correct
                if ($result->isValid()) { 
                    
                    $this->_helper->flashMessenger->addMessage('Logged in!');
                    $this->_redirect('/');
                    return;
                }else{
                    // If the user got login details incorrect
                    $this->_helper->flashMessenger->addMessage('Login details incorrect');
                }
                
                //$this->view->messages = $this->_helper->flashMessenger->getMessages();
                
                //Zend_Debug::dump($values);
                
                
            }// End if form data is valid
        }// End if post data is set
        
    } // End login action


}



