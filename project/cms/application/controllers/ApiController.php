<?php

/*
 * This is the API Controller where admins of the system can add / remove
 * API users to the system
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class ApiController extends Cms_Controllers_Default
{
    
    /*
     * Init function for the controller 
     */
    public function init(){
        parent::init();
    }


    /*
     * This is the action that shows the users the form for editing thier
     * site settings in the CMS
     */
    public function indexAction()
    {
        $this->_redirect('api/manage');
    }
    
    /*
     * Manage the api users action
     * This is only accessable by admins
     * This view gives admins the ability to add / remove / edit api users
     */
    public function manageAction(){
        
        $this->view->pageTitle = 'API Users';
        
        // Get the users form the API
        $users = $this->getFromApi('/api');

        if (isset($users)){
            // Send the users to the view
            $this->view->users = $users;
        }
        
        // Show any flash messages 
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        
    }
    
    /*
     * This is the view for confirming of the user wants to remove a 
     * api user from the system
     */
    public function removeConfirmAction(){
        
        $this->view->pageTitle = 'Remove API User';
        
        // Get the user buy the user ID parsed
        $userID = $this->getRequest()->getParam('id');
        
        // Make sure the user is not being retarded
        if ($userID == 1){
            $this->_helper->flashMessenger->addMessage('Unable to remove that user');
            $this->_redirect('/api/manage');
            return;
        }
        
        if (!$userID){
            $this->_helper->flashMessenger->addMessage('Unable to find API user');
            $this->_redirect('/api/manage');
            return;
        }
        $user = $this->getFromApi('/api/'.$userID);
       
        if ($this->_isMobile){
            $this->_helper->layout->setLayout('dialog-mobile');
        }else{
            $this->_helper->layout->setLayout('dialog');
        }

        if ($user){
            $this->view->user = $user;
        }else{
            $this->_helper->flashMessenger->addMessage('Unable to find API user');
            $this->_redirect('/api/manage');
            return;
        }
    }
    
    /*
     * This is the view action for removing api users from the system
     */
    public function removeAction(){
        
        // Get the user buy the user ID parsed
        $userID = $this->getRequest()->getParam('id');
        
        if ($userID == 1){
            $this->_helper->flashMessenger->addMessage('Cannot remove that user');
            $this->_redirect('/api/manage');
            return;
        }
        
        // If the get param was sent and is in the correct format
        if (isset($userID) && is_numeric($userID)){
            
            // Make post request to remove user from the API
            $removeAction = $this->postToApi('/api', 'remove', $userID);

            if ($removeAction == 1){
                $this->_helper->flashMessenger->addMessage('API user removed from the system');
               
            }else{
                $this->_helper->flashMessenger->addMessage('Could not find the API user to remove');
            }
            
            $this->view->messages = $this->_helper->flashMessenger->getMessages();
            $this->_redirect('/api/manage');
            return;
            
        }else{
            // Redirect back to manage users
            $this->_redirect('/api/manage');
            return;
        }
    }
    
     /*
     * This is the add api user function
     * This is only visible to the admins of the system and is where
     * api users can be added to the system
     */
    public function addAction(){
        
        $this->view->pageTitle = 'Add API User';
        
        // Get an instance of our api form for adding the users
        $apiForm = new Application_Form_ApiForm();
        $apiForm->setMethod('post');
        $apiForm->setElementDecorators($this->_formDecorators);
            
        // Set the active values
        if ($this->_isMobile){
            $apiForm->getElement('key')->setAttrib('placeholder', 'Api Key');
            $apiForm->getElement('ref')->setAttrib('placeholder', 'Api Ref');
        }
        
        $this->view->apiForm = $apiForm;
        
        // Add the api user based on the form post
        if ($this->getRequest()->isPost()){

            // Check if the form data is valid
            if ($apiForm->isValid($_POST)) { 
             
                // Run the add user function at the api
                $addAction = $this->postToApi('/api', 'add', $apiForm->getValues());
 
                // Duplicate entries checking
                if ($addAction != 1){
                    if ($addAction == 'Ref Taken' || $addAction == 'Key Taken'){
                        
                        if ($addAction == 'Ref Taken'){
                            $this->_helper->flashMessenger->addMessage('That ref is taken, please try again');
                        }
                        if ($addAction == 'Key Taken'){
                            $this->_helper->flashMessenger->addMessage('That key is taken, please try again');
                        }
                        $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();

                    }
                }else{
                     // Set the flash message
                    $this->_helper->flashMessenger->addMessage('API user added to the system');
                    $this->view->messages = $this->_helper->flashMessenger->getMessages();
                    $this->_redirect('/api/manage');
                    return;
                }
                return;
            }
        }
    }
    
    /*
     * Edit API users action
     * This view expects an Id param sent to it and is where the
     * users of the sytem can edit a single users details
     */
    public function editAction(){
        
        $this->view->pageTitle = 'Edit API User';

        // Get the user buy the user ID parsed
        $userID = $this->getRequest()->getParam('id');
        
        if ($userID == 1){
            $this->_helper->flashMessenger->addMessage('You cannot edit the system API key');
            $this->_redirect('/api/manage');
            return;
        }
        
        // If the get param was sent and is in the correct format
        if (isset($userID) && is_numeric($userID)){
            
            // Get the user from the API
            $user = $this->getFromApi('/api/'.$userID, 'array');
            
            // Get the user form
            $apiForm = new Application_Form_ApiForm();
            $apiForm->setAction('/cms/api/edit/id/'.$userID);
            $apiForm->setMethod('post');
            $apiForm->setElementDecorators($this->_formDecorators);
            
            // Set the active values
            if ($this->_isMobile){
                $apiForm->getElement('key')->setAttrib('placeholder', 'Api Key');
                $apiForm->getElement('ref')->setAttrib('placeholder', 'Api Ref');
            }
            
            // Update the user based on the form post
            if ($this->getRequest()->isPost()){
            
                // Check if the form data is valid
                if ($apiForm->isValid($_POST)) {

                    $updateAttempt = $this->postToApi('/api', 'update',  $apiForm->getValues(), $userID);
                    
                    // Duplicate entries checking
                    if ($updateAttempt != 1){
                        if ($updateAttempt == 'Ref Taken' || $updateAttempt == 'Key Taken'){

                            if ($updateAttempt == 'Ref Taken'){
                                $this->_helper->flashMessenger->addMessage('That ref is taken, please try again');
                            }
                            if ($updateAttempt == 'Key Taken'){
                                $this->_helper->flashMessenger->addMessage('That key is taken, please try again');
                            }
                            $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
                            $this->_helper->flashMessenger->clearCurrentMessages();

                        }
                    }else{
                        $this->_helper->flashMessenger->addMessage('API user details updated');
                        $this->view->messages = $this->_helper->flashMessenger->getMessages();
                        $this->_redirect('/api/manage');
                        return;
                    }
                    
                }       
            }
            
            // Redirect back to manage users if the user (by the id) was not found
            if ($user == null){
                $this->_redirect('/api/manage');
                return;
            }
            
            
            // Set the values for the form based on the user in the system 
            $apiForm->populate($user);
            
            // Send the form to the view
            $this->view->apiForm = $apiForm;

            $this->view->user = $user;
        }else{
            // Redirect back to manage users
            $this->_redirect('/api/manage');
            return;
        }
        
    }

}