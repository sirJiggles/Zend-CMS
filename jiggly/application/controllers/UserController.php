<?php

class UserController extends Zend_Controller_Action
{

    public function indexAction()
    {
        // action body
        
    }
    
    /*
     * login action
     * create a new login form and if the post value is set validate the user
     * if the user passes validation redirect to the index
     * if not show flash message
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
                
                // Get the values form the form
                $values = $form->getValues();
                
                // Authenitcate the users (from the post data)
                $auth = $this->_authenticateUser($values);
                
                // If the creds are correct
                if ($auth->isValid()) { 
                    
                    $this->_helper->flashMessenger->addMessage('Logged in!');
                    $this->_redirect('/');
                    return;
                }else{
                    // If the user got login details incorrect
                    $this->_helper->flashMessenger->addMessage('Login details incorrect');
                }
                
                // Send flash messages to the view
                $this->view->messages = $this->_helper->flashMessenger->getMessages();
 
            }// End if form data is valid
        }// End if post data is set
        
    } // End login action
    
    /*
     * Logout action, clear the identity and return to index page
     */
    public function logoutAction()
    {
        Zend_Auth::getInstance()->clearIdentity();
        $this->_redirect('/');
        return;
    }
    
    /*
     * Manage the users action
     * This is only accessable by admins
     * This view gives admins the ability to add / remove / edit users
     */
    public function manageAction(){
        
        // Get new instance of the users model and fetch all users in the system
        $usersModel = new Application_Model_User();
        $users = $usersModel->getAllUsers();
        
        // Send the users to the view
        $this->view->users = $users;
    }

    
    /*
     * authenticate user function
     * This function validates the user in the users table using the post data
     * from the login form
     * 
     * @param array $postData
     * @return Zend_Auth_Adapter_Interface $result
     */
    protected function _authenticateUser(array $postData)
    {

        try{
            // Get the database adapter
            $db = $this->_getParam('db');

            // Create a new instance of the auth adapter, letting it know how we will treat the creds
            $adapter = new Zend_Auth_Adapter_DbTable(
                $db,
                'users',
                'username',
                'password',
                'SHA1(?)'
            );

            // Set the identity and credencial values for the auth adapter 
            $adapter->setIdentity($postData['username']);
            // String appended to password is salt, not perfect but works :)
            $adapter->setCredential($postData['password']."34idnTgs98");

            // Get an instance of Zend_Auth
            $auth   = Zend_Auth::getInstance();
            // Check if the values in the adapter are correct (authenticate)
            $result = $auth->authenticate($adapter);
            
            /*
             * If the result is valid add the data about the user to the 
             * authentification storage (all but the password)
             */
            if ($result->isValid()){
                $data = $adapter->getResultRowObject(null, 'password');
                $auth->getStorage()->write($data);
            }
            
            // Return the result to the login action
            return $result;
            
        }catch(Exception $e){
            throw 'Unable to authenticate user: '.$e->getMessage();
        } 
    }

}