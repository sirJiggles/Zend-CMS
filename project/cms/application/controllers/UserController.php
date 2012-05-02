<?php

/*
 * This is the user controller, this controller handles the login / logout
 * functionality as well as dealing with things like forgotten password,
 * password reset and all other user specific functionality
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class UserController extends Cms_Controllers_Default
{
    
    /*
     * Init function for the controller 
     */
    public function init(){

        parent ::init();
       
    }



    public function indexAction()
    {
        // action body
        $this->_redirect('user/manage');
    }
    
    /*
     * login action
     * create a new login form and if the post value is set validate the user
     * if the user passes validation redirect to the index
     * if not show flash message
     */
    public function loginAction()
    {
        $this->view->pageTitle = 'Login';
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
        
        $this->view->pageTitle = 'Manage Users';
        
        // Get the users form the API
        $users = $this->getFromApi('/user');
        
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
        
        $this->view->pageTitle = 'Edit User';

        // Get the user buy the user ID parsed
        $userID = $this->getRequest()->getParam('id');
        
        // If the get param was sent and is in the correct format
        if (isset($userID) && is_numeric($userID)){
            
           
            
            // Get the user from the API
            $user = $this->getFromApi('/user/'.$userID, 'array');
            
            // Get the user form
            $userForm = new Application_Form_UserForm();
            $userForm->setAction('/cms/user/edit/id/'.$userID);
            $userForm->setMethod('post');
            
            // Update the user based on the form post
            if ($this->getRequest()->isPost()){
            
                // Check if the form data is valid
                if ($userForm->isValid($_POST)) {

                    $updateAttempt = $this->postToApi('/user', 'update',  $userForm->getValues(), $userID);
                    
                    // Duplicate entries checking
                    if ($updateAttempt != 1){
                        if ($updateAttempt == 'Name Taken' || $updateAttempt == 'Email Taken'){

                            if ($updateAttempt == 'Name Taken'){
                                $this->_helper->flashMessenger->addMessage('That username is taken, please try again');
                            }
                            if ($updateAttempt == 'Email Taken'){
                                $this->_helper->flashMessenger->addMessage('That email address is taken, please try again');
                            }
                            $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
                            $this->_helper->flashMessenger->clearCurrentMessages();

                        }
                    }else{
                        $this->_helper->flashMessenger->addMessage('User details updated');
                        $this->view->messages = $this->_helper->flashMessenger->getMessages();
                        $this->_redirect('/user/manage');
                        return;
                    }
                    
                }       
            }
            
            // Redirect back to manage users if the user (by the id) was not found
            if ($user == null){
                $this->_redirect('/user/manage');
                return;
            }
            
            
            // Set the values for the form based on the user in the system 
            $userForm->populate($user);
            
            // Send the form to the view
            $this->view->userForm = $userForm;

            $this->view->user = $user;
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
        
        $this->view->pageTitle = 'Add User';
        
        // Get an instance of our user form for adding the users
        $userForm = new Application_Form_UserForm();
        $userForm->setMethod('post');
        
        // Set password as required for new users
        $userForm->getElement('password')->setRequired(true);
        $userForm->getElement('password_repeat')->setRequired(true);
        
        $this->view->userForm = $userForm;
        
        // Add the user based on the form post
        if ($this->getRequest()->isPost()){
            // Check if the form data is valid
            if ($userForm->isValid($_POST)) { 
                // Run the add user function at the api
                $addAction = $this->postToApi('/user', 'add', $userForm->getValues());
 
                // Duplicate entries checking
                if ($addAction != 1){
                    if ($addAction == 'Name Taken' || $addAction == 'Email Taken'){
                        
                        if ($addAction == 'Name Taken'){
                            $this->_helper->flashMessenger->addMessage('That username is taken, please try again');
                        }
                        if ($addAction == 'Email Taken'){
                            $this->_helper->flashMessenger->addMessage('That email address is taken, please try again');
                        }
                        $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();

                    }
                }else{
                     // Set the flash message
                    $this->_helper->flashMessenger->addMessage('User added to the system');
                    $this->view->messages = $this->_helper->flashMessenger->getMessages();
                    $this->_redirect('/user/manage');
                    return;
                }
                
 
                return;
            }
        }
    }
    
    /*
     * This is the view for confirming of the user wants to remove a user from the system
     */
    public function removeConfirmAction(){
        
        $this->view->pageTitle = 'Remove User';
        
        // Get the user buy the user ID parsed
        $userID = $this->getRequest()->getParam('id');
        
        if (!$userID ){
            $this->_helper->flashMessenger->addMessage('Unable to find user');
            $this->_redirect('/user/manage');
            return;
        }
        $user = $this->getFromApi('/user/'.$userID);
        
        $this->_helper->layout->setLayout('dialog');

        if ($user){
            $this->view->user = $user;
        }else{
            $this->_helper->flashMessenger->addMessage('Unable to find user');
            $this->_redirect('/user/manage');
            return;
        }
    }
    
    
    /*
     * This is the view action for removing users from the system
     */
    public function removeAction(){
        
        // Get the user buy the user ID parsed
        $userID = $this->getRequest()->getParam('id');
        
        // If the get param was sent and is in the correct format
        if (isset($userID) && is_numeric($userID)){
            
            // Make post request to remove user from the API
            $removeAction = $this->postToApi('/user', 'remove', $userID);

            if ($removeAction == 1){
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
        
        $this->view->pageTitle = 'Forgot Password';
        
        // Get the forgot password form and display it
        $passwordForm = new Application_Form_ForgotPassword();
        $passwordForm->setMethod('post');
        
        // Send it on up to the view
        $this->view->passswordForm = $passwordForm;
        
        if ($this->getRequest()->isPost()){

            // Check if the form data is valid
            if ($passwordForm->isValid($_POST)) {
                
                // Get the user by the email address from the api
                $foundUser = $this->getFromApi('/user/email/'.$passwordForm->getValue('email'));
                
                if (isset($foundUser)){
                    // Set the flash message
                    $this->_helper->flashMessenger->addMessage('An email has been sent to your account');
                    $this->view->messages = $this->_helper->flashMessenger->getMessages();
                    
                    /* 
                     * Here we generate a new link for the user to click on to activate thier new password as we don't want
                     * the user to have the new password sent to their email address in plain text.
                     */
                    $newPasswordLink = $this->_generatePasswordLink($foundUser->id);
                    
                    
                    // Create an instance of our view template for the email and send the params
                    $emailTemplateView = new Zend_View();
                    $emailTemplateView->setScriptPath(APPLICATION_PATH . '/views/emails/');
                    
                    $userName = $foundUser->first_name.' '.$foundUser->last_name;
                    
                    // Assign valeues
                    $emailTemplateView->assign('name', $userName);
                    $emailTemplateView->assign('passwordLink', $newPasswordLink);
                    $emailTemplateView->assign('username', $foundUser->username);
                    
                    // Using the template and the values create an instance of the rendered view that is going to 
                    // go in the email
                    $bodyHtml = $emailTemplateView->render('forgoten-password.phtml');
                    
                    // Only send the email if not in test mode
                    if($_SERVER['SERVER_NAME'] != 'jiggly.dev'){
                        // Send the email with the link to activate the new password
                        $mail = new Zend_Mail();
                        //$mail->setBodyText('My Nice Test Text');
                        $mail->setBodyHtml($bodyHtml);
                        $mail->setFrom('jiggly@cms.com', 'Jiggly CMS');
                        //$mail->
                        $mail->addTo($foundUser->email_address, $userName);
                        $mail->setSubject('Jiggly CMS - Password Reset');
                        $mail->send();
                    }
                    
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
     * Function for activating the users passwords
     * 
     * This function gets the request url, splits it up and checks if it matches the hash in the db
     * if the hash and id combo is valid then we present the user with a reset password form
     * after the form is submitted we set the new password and remove the has from the users record
     * 
     */
    public function activatePasswordAction(){
        
        $this->view->pageTitle = 'Activate Password';
        
        $oneDirection = false;
        
        // Get the hash from the request param
        $hash = $this->getRequest()->getParam('req');
        
        
        // Get a new change password form
        $changePasswordForm = new Application_Form_ChangePassword();
        $changePasswordForm->setMethod('post');

        // If the get param was sent and is in the correct format
        if (isset($hash) && $hash != ''){
 
            // Validate that the hash is correct based on the users ID etc
            $validateHash = $this->_validateHash($hash);
            
            // If the hash was found in the db
            if ($validateHash){
                
                // Set the hidden hash element to be the value of the current users hash (so we can validate on post req)
                $changePasswordForm->getElement('hash')->setValue($hash);
                
                // Send the view to the form
                $this->view->form = $changePasswordForm;
                
            }else{
                $this->_invalidPasswordResetValidation();
            }
            
            $oneDirection = true;

        }
        
       
        // Handle the password reset form post
        if ($this->getRequest()->isPost()){
 
            // Chnage users password
            if ($changePasswordForm->isValid($_POST)) {

                // Get the form values
                $formValues = $changePasswordForm->getValues();
                
                // Check the hash value again from the hidden post input
                $validateHash = $this->_validateHash($formValues['hash']);

                if ($validateHash){
                    
                    // Change users account details as per thier post data in the api
                    $this->postToApi('/user', 'updateForgotPassword', $formValues);
                    
                    // Redirect back to login page and let the user know the passwprd has been reset
                    $this->_helper->flashMessenger->addMessage('Password changed');
                    $this->view->messages = $this->_helper->flashMessenger->getMessages();
                    $attemptsSession = new Zend_Session_Namespace('attempts');
                    $attemptsSession->tries = 0;
                    $this->_redirect('/user/login');

                    return;

                }else{
                    $this->_invalidPasswordResetValidation();
                }


            }
            
            // Send the view to the form
            $this->view->form = $changePasswordForm;
            
            $oneDirection = true;

        }
        
        // Handle people navigating to this page by mistake
        if (!$oneDirection){
            $this->_invalidPasswordResetValidation();
        }
        
    }
    
    /*
     * This is function to set the flash messages for incorrect validation
     * in the forgotten password section
     */
    protected function _invalidPasswordResetValidation(){
        $this->_helper->flashMessenger->addMessage('Please use the link from your email');
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        $this->_redirect('/user/login');
        return;
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
            $dataBase = $this->_getParam('db');

            // Create a new instance of the auth adapter, letting it know how we will treat the creds
            $adapter = new Zend_Auth_Adapter_DbTable(
                $dataBase,
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
    
    
    /*
     * This is the function that generates new password links that go into the
     * forgotten passowrd email content
     * 
     * @param int $userId
     * @return string $passwordLink
     */
    protected function _generatePasswordLink($userId){
        
        
        try{
            // Sanity checking
            if (is_numeric($userId)){
               
                $randomHash = mt_rand();
                
                // Update the users password hash using the API
                $this->postToApi('/user', 'updatePasswordHash', $userId, $randomHash);
                
                // Construct the new password link
                $newPasswordLink = 'http://' . $_SERVER['SERVER_NAME'].'/user/activate-password/req/'.$userId.':'.$randomHash;

                
                return $newPasswordLink;
                
            }else{
                throw new Exception('Incorrect arguments passed to User::_generatePasswordLink, expected userId type int');
            }

            
        }catch(Exception $e){
            echo 'Unable to generate a password link: '.$e->getMessage();
        }

        
    }
    
    /*
     * This function validates the hash that has been set to the activate
     * password action. It checks the database for the user id and forgot_password_hash value
     * 
     * @param mixed $hash
     * @return boolean $result
     */
    protected function _validateHash($hash){
        
        try{
            
            // Break the hash into parts based on the : symbol, then using this get the id and hash
            $parts = explode(":", $hash);
            $userId = $parts[0];
            $hashPure = $parts[1];
            
            // Validate hash from the api
            $result = $this->postToApi('/user', 'validateHash', $userId, $hashPure);
            
            // Return the result
            return $result;
            
            
        }catch(Exception $e){
            echo "Unable to validate the hash in User::_validateHash: ".$e->getMessage();
        }
    }

}