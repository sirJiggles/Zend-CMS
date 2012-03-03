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
        
        // Disable the layout for this view
        $this->_helper->layout()->disableLayout();
        
        // Send the form to the view
        $this->view->loginForm = $form;
        
        // If the user has posted the form
        if ($this->getRequest()->isPost()){
            
            
            // Init the capture tries, if not set set as 0
            $captcha_session = new Zend_Session_Namespace('captcha');
            if (empty($captcha_session->tries)){ 
                $captcha_session->tries = 0;
            }
		
            // Check if the form data is valid
            if ($form->isValid($_POST)) {
                
                // Get the values form the form
                $values = $form->getValues();
                
                // Authenitcate the users (from the post data)
                $auth = $this->_authenticateUser($values);
                
                // If the creds are correct
                if ($auth->isValid()) { 
                    
                    // Reset captcha to 0 as they have logged in correctly
                    $captcha_session->tries = 0;
                    
                    $this->_helper->flashMessenger->clearCurrentMessages();
                    
                    $this->_redirect('/');
                    return;
                }else{
                    // If the user got login details incorrect
                   
                    $this->_helper->flashMessenger->addMessage('Login detailss incorrect');
                    // Send flash messages to the view
                    $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
                    
                    // Set as +1 for attempts
                    $captcha_session->tries = $captcha_session->tries + 1;
 
                }

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
        
        /*
         * This should NEVER happen, but if there are no users in the system
         * and they have somehow logged in, redirect them back to index
         */
        if (isset($users)){
            // Send the users to the view
            $this->view->users = $users;
        }
    }
    
    
    /*
     * Edit users action
     * This view expects an Id param sent to it and is where the
     * users of the sytem can edit a single users details
     */
    public function editAction(){
        
        // Get the user buy the user ID parsed
        $id = $this->getRequest()->getParam('id');
        
        // If the get param was sent and is in the correct format
        if (isset($id) && is_numeric($id)){
            $userModel = new Application_Model_User();
            $user = $userModel->getUserById($id);
            
            // Get the user form
            $userForm = new Application_Form_UserForm();
            $userForm->setAction('/user/edit/id/'.$id);
            $userForm->setMethod('post');
            
            // Update the user based on the form post
            if ($this->getRequest()->isPost()){
            
                // Check if the form data is valid
                if ($userForm->isValid($_POST)) {
                    $userModel->updateUser($userForm->getValues(), $id);
                    // Fetch the updated user
                    $user = $userModel->getUserById($id);
                    
                    // Set the flash message
                    $this->_helper->flashMessenger->addMessage('User details updated');
                    
                    // Send flash messages to the view
                    $this->view->messages = $this->_helper->flashMessenger->getMessages();
                    
                }
            }
            
            // Set the values for the form based on the user in the system 
            $userForm->populate($user->toArray());
            
            // Send the form to the view
            $this->view->userForm = $userForm;
            
            // Redirect back to manage users if the user (by the id) was not found
            if (isset($user)){
                $this->view->user = $user;
            }else{
                $this->_redirect('/user/manage');
                return;
            }
        }else{
            // Redirect back to manage users
            $this->_redirect('/user/manage');
            return;
        }
        
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