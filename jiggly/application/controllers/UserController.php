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
        
        // Disable the layout for this view
        $this->_helper->layout()->setLayout('login');
        
        // Init the tries, if not set set as 0
        $attemptsSession = new Zend_Session_Namespace('attempts');
        if (empty($attemptsSession->tries)){ 
            $attemptsSession->tries = 0;
        }
        
        if ($attemptsSession->tries > 2){
            $this->view->forgotPasswordLink = true;
        }else{
            
            // Get a new instance of the login form an set the prams action and method
            $form = new Application_Form_Login();
            $form->setAction('/login');
            $form->setMethod('post');

            // Send the form to the view
            $this->view->loginForm = $form;
 
        }
        
        // Show any flash messages (this can be overriden later) 
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        
        // If the user has posted the form (and the attempts is les than || == 2
        if ($this->getRequest()->isPost() && $attemptsSession->tries <= 2){
            
		
            // Check if the form data is valid
            if ($form->isValid($_POST)) {
                
                // Get the values form the form
                $values = $form->getValues();
                
                // Authenitcate the users (from the post data)
                $auth = $this->_authenticateUser($values);
                
                // If the creds are correct
                if ($auth->isValid()) { 
                    
                    // Reset attempts to 0 as they have logged in correctly
                    $attemptsSession->tries = 0;
                    
                    $this->_helper->flashMessenger->clearCurrentMessages();
                    
                    $this->_redirect('/');
                    return;
                }else{
                    // If the user got login details incorrect
                   
                    $this->_helper->flashMessenger->addMessage('Login details incorrect');
                    // Send flash messages to the view
                    $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();

                    // Set as +1 for attempts
                    $attemptsSession->tries = $attemptsSession->tries + 1;
                    
 
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
        
        // Show any flash messages 
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
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
                    $updateAttempt = $userModel->updateUser($userForm->getValues(), $id);
                    
                    if ($updateAttempt){
                        // Fetch the updated user
                        $user = $userModel->getUserById($id);
                        $this->_helper->flashMessenger->addMessage('User details updated');
                        $this->view->messages = $this->_helper->flashMessenger->getMessages();
                        $this->_redirect('/user/manage');
                        return;
                    }else{
                        $this->_helper->flashMessenger->addMessage('That username is alrady taken, please try again');
                    }
                    
                    // Send flash messages to the view
                    $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
                    $this->_helper->flashMessenger->clearCurrentMessages();
                    
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
     * This is the add user function
     * This is only visible to the admins of the system and is where
     * users can be added to the system
     */
    public function addAction(){
        
        // Get an instance of our user form for adding the users
        $userForm = new Application_Form_UserForm();
        $userForm->setAction('/user/add');
        $userForm->setMethod('post');
        
        // Set password as required for new users
        $userForm->getElement('password')->setRequired(true);
        $userForm->getElement('password_repeat')->setRequired(true);
        
        $this->view->userForm = $userForm;
        
        // Add the user based on the form post
        if ($this->getRequest()->isPost()){

            // Check if the form data is valid
            if ($userForm->isValid($_POST)) {
                
                // Get an instance of the user model
                $userModel = new Application_Model_User();
                // Run the add user function with the form post values
                $addAction = $userModel->addUser($userForm->getValues());
                
                if ($addAction){
                    // Set the flash message
                    $this->_helper->flashMessenger->addMessage('User added to the system');
                    $this->view->messages = $this->_helper->flashMessenger->getMessages();
                    $this->_redirect('/user/manage');
                    return;
                }else{
                    $this->_helper->flashMessenger->addMessage('That username is taken, please try again');
                    $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
                    
                }
 
                return;
            }
        }
    }
    
    /*
     * This is the view action for removing users from the system
     */
    public function removeAction(){
        // Get the user buy the user ID parsed
        $id = $this->getRequest()->getParam('id');
        
        // If the get param was sent and is in the correct format
        if (isset($id) && is_numeric($id)){
            
            $userModel = new Application_Model_User();
            $removeStatus = $userModel->removeUser($id);
            
            if ($removeStatus){
                $this->_helper->flashMessenger->addMessage('User removed from the system');
               
            }else{
                $this->_helper->flashMessenger->addMessage('Could not find the user to remove');
            }
            
            $this->view->messages = $this->_helper->flashMessenger->getMessages();
            $this->_redirect('/user/manage');
            return;
            
        }else{
            // Redirect back to manage users
            $this->_redirect('/user/manage');
            return;
        }
    }
    
    
    /*
     * This is the forgotten password action
     */
    public function forgotPasswordAction(){
        
        // Get the forgot password form and display it
        $passwordForm = new Application_Form_ForgotPassword();
        $passwordForm->setAction('/user/forgot-password');
        $passwordForm->setMethod('post');
        
        // Send it on up to the view
        $this->view->passswordForm = $passwordForm;
        
        if ($this->getRequest()->isPost()){

            // Check if the form data is valid
            if ($passwordForm->isValid($_POST)) {

                // Get an instance of the user model
                $userModel = new Application_Model_User();
                
                // Get the user by the email address
                $foundUser = $userModel->getByEmailAddress($passwordForm->getValue('email'));

                if (isset($foundUser)){
                    // Set the flash message
                    $this->_helper->flashMessenger->addMessage('An email has been sent to your account');
                    $this->view->messages = $this->_helper->flashMessenger->getMessages();
                    
                    
                    // Send the email with the link to activate the new password
                    
                    
                    // Clear the session attempts value (let them try login again with new password)
                    $attemptsSession = new Zend_Session_Namespace('attempts');
                    $attemptsSession->tries = 0;
                    
                    $this->_redirect('/user/login');
                    
                    return;
                }else{
                    $this->_helper->flashMessenger->addMessage('Unable to find that email address in the system');
                    $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();

                }

                return;
            }
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
                'SHA1(?) AND active = 1'
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
            echo 'Unable to authenticate user: '.$e->getMessage();
        } 
    }

}