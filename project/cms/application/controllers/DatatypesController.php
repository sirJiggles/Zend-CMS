<?php

/*
 * This is where superadmins can add / edit content types
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class DatatypesController extends Cms_Controllers_Default
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
        $this->view->pageTitle = 'Content Types';
        
        // Get all the content types from the API
        $dataTypes = $this->getFromApi('/datatypes');
        
        $this->view->dataTypes = $dataTypes;
        
        // Show any flash messages 
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        
    }
    
    /*
     * This is the add action where superadmins can add datatypes to the system
     */
    public function addAction(){
        $this->view->pageTitle = 'Add Content Type';
        
        // Get an instance of our user form for adding the users
        $contentTypeForm = new Application_Form_ContentTypeForm();
        $contentTypeForm->setMethod('post');
        
        $contentTypeForm->setElementDecorators($this->_formDecorators);
        
        // Set the active values
        if ($this->_isMobile){
            
            // Set the placeholder texts
            $contentTypeForm->getElement('name')->setAttrib('placeholder', 'Name');
           
        }
        
        $this->view->contentTypeForm = $contentTypeForm;
        
        // Add the user based on the form post
        if ($this->getRequest()->isPost()){
           
            // Check if the form data is valid
            if ($contentTypeForm->isValid($_POST)) { 
                // Run the add user function at the api
                $addAction = $this->postToApi('/datatypes', 'add', $contentTypeForm->getValues());
 
                // Duplicate entries checking
                if ($addAction != 1){
                    if ($addAction == 'Name Taken'){

                        $this->_helper->flashMessenger->addMessage('That name is taken, please try again');
                        $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
                        $this->_redirect('/datatypes');
                    }
                }else{
                     // Set the flash message
                    $this->_helper->flashMessenger->addMessage('Content type added to the system');
                    $this->view->messages = $this->_helper->flashMessenger->getMessages();
                    $this->_redirect('/datatypes');
                    return;
                }
                
 
                return;
            }
        }
        
    }
    
    /*
     * Edit content types action, this is where the superadmins can edit the
     * name of the content type if they wish
     */
    public function editAction(){
        
        
        $this->view->pageTitle = 'Rename Content Type';

        // Get the content type buy the ID parsed
        $id = $this->getRequest()->getParam('id');
        
        // If the get param was sent and is in the correct format
        if (isset($id) && is_numeric($id)){

            // Get the content type from the API
            $contentType = $this->getFromApi('/datatypes/'.$id, 'array');
            

            // Get the content type form
            $contentTypeForm = new Application_Form_ContentTypeForm();
            $contentTypeForm->setAction('/cms/datatypes/edit/id/'.$id);
            $contentTypeForm->setMethod('post');
            $contentTypeForm->setElementDecorators($this->_formDecorators);
            
            // Set the active values
            if ($this->_isMobile){
                $contentTypeForm->getElement('name')->setAttrib('placeholder', 'Name');
            }
            
            // Update the content type based on the form post
            if ($this->getRequest()->isPost()){
                
                // Check if the form data is valid
                if ($contentTypeForm->isValid($_POST)) {

                    $updateAttempt = $this->postToApi('/datatypes', 'update',  $contentTypeForm->getValues(), $id);
                    
                    // Duplicate entries checking
                    if ($updateAttempt != 1){
                        if ($updateAttempt == 'Name Taken'){

                            $this->_helper->flashMessenger->addMessage('That name is taken, please try again');
                            $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
                            $this->_redirect('/datatypes');

                        }
                    }else{
                        $this->_helper->flashMessenger->addMessage('Content Type details updated');
                        $this->view->messages = $this->_helper->flashMessenger->getMessages();
                        $this->_redirect('/datatypes');
                        return;
                    }
                    
                }       
            }
            
            // Redirect back to manage content types if the content type (by the id) was not found
            if ($contentType === null){
                $this->_redirect('/datatypes');
                return;
            }
            
            
            // Set the values for the form based on the content type in the system 
            $contentTypeForm->populate($contentType);
            
            // Send the form to the view
            $this->view->contentTypeForm = $contentTypeForm;

            $this->view->contentType = $contentType;
        }else{
            // Redirect back to content types
            $this->_redirect('/datatypes');
            return;
        }
        
    }
    
     /*
     * This is the view for confirming the removal of the content type
     */
    public function removeConfirmAction(){
        
        $this->view->pageTitle = 'Remove Content Type';
        
        // Get the content type buy the id parsed
        $id = $this->getRequest()->getParam('id');
        
        if (!$id ){
            $this->_helper->flashMessenger->addMessage('Unable to find content type');
            $this->_redirect('/datatypes');
            return;
        }
        $contentType = $this->getFromApi('/datatypes/'.$id);
        
        
        if ($this->_isMobile){
            $this->_helper->layout->setLayout('dialog-mobile');
        }else{
            $this->_helper->layout->setLayout('dialog');
        }
        
        if ($contentType){
            $this->view->contentType = $contentType;
        }else{
            $this->_helper->flashMessenger->addMessage('Unable to find content type');
            $this->_redirect('/datatypes');
            return;
        }
    }
    
    
    /*
     * This is the view action for removing content types from the system
     */
    public function removeAction(){
        
        // Get the content type buy the ID parsed
        $id = $this->getRequest()->getParam('id');
        
        // If the get param was sent and is in the correct format
        if (isset($id) && is_numeric($id)){
            
            // Make post request to remove content type from the API
            $removeAction = $this->postToApi('/datatypes', 'remove', $id);

            if ($removeAction == 1){
                $this->_helper->flashMessenger->addMessage('Content type removed from the system');
               
            }else{
                $this->_helper->flashMessenger->addMessage('Could not find the content type to remove');
            }
            
            $this->view->messages = $this->_helper->flashMessenger->getMessages();
            $this->_redirect('/datatypes');
            return;
            
        }else{
            // Redirect back to content types
            $this->_redirect('/datatypes');
            return;
        }
    }

}