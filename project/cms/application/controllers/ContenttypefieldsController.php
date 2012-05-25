<?php

/*
 * This is where superadmins of the system can edit content type fields for their
 * content types they have created 
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class ContenttypefieldsController extends Cms_Controllers_Default
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
        $this->view->pageTitle = 'Content Type Fields';
        
        $id = $this->getRequest()->getParam('id');
        
        // If the get param was sent and is in the correct format
        if (isset($id) && is_numeric($id)){
            
            // Get the data type from the API
            $contentType = $this->getFromApi('/contenttypes/'.$id, 'array');
          
            // Send the content type to the view
            $this->view->contentType = $contentType;
            
            // Get all the content types from the API
            $contentTypeFields = $this->getFromApi('/contenttypefields/contenttype/'.$id);

            // If we could not find the fields for that content type!
            if ($contentTypeFields === null){
                
                $this->_helper->flashMessenger->addMessage('There are currently no fields for this content type');
                $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
                return;
            }
            
            // Send the data to the view
            $this->view->contentTypeFields = $contentTypeFields;
            // Show any flash messages 
            $this->view->messages = $this->_helper->flashMessenger->getMessages();
        }else{
            $this->_helper->flashMessenger->addMessage('No Content Type ID Passed');
            $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
            $this->_redirect('/contenttypes');
            return;
        }
        
    }
    
    /*
     * This is the add action where superadmins can add datatype fields to the system
     */
    public function addAction(){
        $this->view->pageTitle = 'Add Content Type Field';
        
        // Keep reference to the content type, if not sent send them back to content types
        $contentTypeIdent = $this->getRequest()->getParam('content-type');
        
        if (isset($contentTypeIdent) && is_numeric($contentTypeIdent)){
            $contentType = $this->getFromApi('/contenttypes/'.$contentTypeIdent, 'array');
            $this->view->contentType = $contentType;
        }else{
            $this->_helper->flashMessenger->addMessage('Unkown content type sent cannot add field');
            $this->view->messages = $this->_helper->flashMessenger->getMessages();
            $this->_redirect('/contenttypes');
            return;
        }
        
        
        // Get an instance of our user form for adding the users
        $contentTypeFieldForm = new Application_Form_ContentTypeFieldForm();
        $contentTypeFieldForm->setMethod('post');
        
        $contentTypeFieldForm->getElement('content_type')->setValue($contentTypeIdent);
        
        $contentTypeFieldForm->setElementDecorators($this->_formDecorators);
        
        // Set the active values
        if ($this->_isMobile){
            
            // Set the placeholder texts
            $contentTypeFieldForm->getElement('name')->setAttrib('placeholder', 'Name');
            $contentTypeFieldForm->getElement('format')->setAttrib('placeholder', 'Type');
        }
        
        $this->view->contentTypeFieldForm = $contentTypeFieldForm;
        
        // Add the user based on the form post
        if ($this->getRequest()->isPost()){
           
            // Check if the form data is valid
            if ($contentTypeFieldForm->isValid($_POST)) { 
                // Run the add user function at the api
                $addAction = $this->postToApi('/contenttypefields', 'add', $contentTypeFieldForm->getValues());
 
                // Duplicate entries checking
                if ($addAction != 1){
                    if ($addAction == 'Name Taken'){

                        $this->_helper->flashMessenger->addMessage('That name is taken, please try again');
                        $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
                        $this->_redirect('/contenttypefields/index/id/'.$contentTypeIdent);
                    }
                }else{
                     // Set the flash message
                    $this->_helper->flashMessenger->addMessage(ucfirst($contentType['name']).' field added to the system');
                    $this->view->messages = $this->_helper->flashMessenger->getMessages();
                    $this->_redirect('/contenttypefields/index/id/'.$contentTypeIdent);
                    return;
                }
                
 
                return;
            }
        }
        
    }
    
    /*
     * Edit content types action, this is where the superadmins can edit the
     * content type fields if they wish
     */
    public function editAction(){
        
        
        $this->view->pageTitle = 'Edit Content Type Field';
        
        // Keep reference to the content type, if not sent send them back to content types
        $contentTypeIdent = $this->getRequest()->getParam('content-type');
        
        if (isset($contentTypeIdent) && is_numeric($contentTypeIdent)){
            $contentType = $this->getFromApi('/contenttypes/'.$contentTypeIdent, 'array');
            $this->view->contentType = $contentType;
        }else{
            $this->_helper->flashMessenger->addMessage('Unkown content type sent cannot add field');
            $this->view->messages = $this->_helper->flashMessenger->getMessages();
            $this->_redirect('/contenttypes');
            return;
        }

        // Get the content type buy the ID parsed
        $id = $this->getRequest()->getParam('id');
        
        // If the get param was sent and is in the correct format
        if (isset($id) && is_numeric($id)){

            // Get the content type from the API
            $contentTypeField = $this->getFromApi('/contenttypefields/'.$id, 'array');

            // Get the content type field form
            $contentTypeFieldForm = new Application_Form_ContentTypeFieldForm();
            $contentTypeFieldForm->setAction('/cms/contenttypefields/edit/id/'.$id.'/content-type/'.$contentTypeIdent);
            $contentTypeFieldForm->setMethod('post');
            $contentTypeFieldForm->getElement('content_type')->setValue($contentTypeIdent);
            $contentTypeFieldForm->setElementDecorators($this->_formDecorators);
            
            // Set the active values
            if ($this->_isMobile){
                $contentTypeFieldForm->getElement('name')->setAttrib('placeholder', 'Name');
                $contentTypeFieldForm->getElement('format')->setAttrib('placeholder', 'Type');
            }
            
            // Update the content type field based on the form post
            if ($this->getRequest()->isPost()){
                
                // Check if the form data is valid
                if ($contentTypeFieldForm->isValid($_POST)) {

                    $updateAttempt = $this->postToApi('/contenttypefields', 'update',  $contentTypeFieldForm->getValues(), $id);
                    
                    // Duplicate entries checking
                    if ($updateAttempt != 1){
                        if ($updateAttempt == 'Name Taken'){

                            $this->_helper->flashMessenger->addMessage('That name is taken, please try again');
                            $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
                            $this->_helper->flashMessenger->clearCurrentMessages();
                            $this->_redirect('/contenttypefields/index/id/'.$contentTypeIdent);

                        }
                    }else{
                        $this->_helper->flashMessenger->addMessage(ucfirst($contentType['name']).' field details updated');
                        $this->view->messages = $this->_helper->flashMessenger->getMessages();
                        $this->_redirect('/contenttypefields/index/id/'.$contentTypeIdent);
                        return;
                    }
                    
                }       
            }
            
            // Redirect back to manage content types if the content type (by the id) was not found
            if ($contentTypeField === null){
                $this->_redirect('/contenttypes');
                return;
            }
            
            
            // Set the values for the form based on the content type field in the system 
            $contentTypeFieldForm->populate($contentTypeField);
            
            // Send the form to the view
            $this->view->contentTypeFieldForm = $contentTypeFieldForm;

            $this->view->contentTypeField = $contentTypeField;
        }else{
            // Redirect back to content types
            $this->_redirect('/contenttypes');
            return;
        }
        
    }
    
     /*
     * This is the view for confirming the removal of the content type
     */
    public function removeConfirmAction(){
        
        $this->view->pageTitle = 'Remove Content Type Field';
        
        // Keep reference to the content type, if not sent send them back to content types
        $contentTypeIdent = $this->getRequest()->getParam('content-type');
        
        if (isset($contentTypeIdent) && is_numeric($contentTypeIdent)){
            $contentType = $this->getFromApi('/contenttypes/'.$contentTypeIdent, 'array');
            $this->view->contentType = $contentType;
        }else{
            $this->_helper->flashMessenger->addMessage('Unkown content type sent cannot add field');
            $this->view->messages = $this->_helper->flashMessenger->getMessages();
            $this->_redirect('/contenttypes');
            return;
        }
        
        // Get the content type field buy the id parsed
        $id = $this->getRequest()->getParam('id');
        
        if (!$id ){
            $this->_helper->flashMessenger->addMessage('Unable to find '.ucfirst($contentType['name']).' field');
            $this->_redirect('/contenttypes');
            return;
        }
        $contentTypeField = $this->getFromApi('/contenttypefields/'.$id);
        
        
        if ($this->_isMobile){
            $this->_helper->layout->setLayout('dialog-mobile');
        }else{
            $this->_helper->layout->setLayout('dialog');
        }
        
        if ($contentTypeField){
            $this->view->contentTypeField = $contentTypeField;
        }else{
            $this->_helper->flashMessenger->addMessage('Unable to find '.ucfirst($contentType['name']).' field');
            $this->_redirect('/contenttypes');
            return;
        }
    }
    
    
    /*
     * This is the view action for removing content type fields from the system
     */
    public function removeAction(){
        
         // Keep reference to the content type, if not sent send them back to content types
        $contentTypeIdent = $this->getRequest()->getParam('content-type');
        
        if (isset($contentTypeIdent) && is_numeric($contentTypeIdent)){
            $contentType = $this->getFromApi('/contenttypes/'.$contentTypeIdent, 'array');
            $this->view->contentType = $contentType;
        }else{
            $this->_helper->flashMessenger->addMessage('Unkown content type sent cannot add field');
            $this->view->messages = $this->_helper->flashMessenger->getMessages();
            $this->_redirect('/contenttypes');
            return;
        }
        
        // Get the content type buy the ID parsed
        $id = $this->getRequest()->getParam('id');
        
        // If the get param was sent and is in the correct format
        if (isset($id) && is_numeric($id)){
            
            // Make post request to remove content type from the API
            $removeAction = $this->postToApi('/contenttypefields', 'remove', $id);
            

            if ($removeAction == 1){
                $this->_helper->flashMessenger->addMessage(ucfirst($contentType['name']).' field removed from the system');
               
            }else{
                $this->_helper->flashMessenger->addMessage('Could not find the '.ucfirst($contentType['name']).' field to remove');
            }
            
            $this->view->messages = $this->_helper->flashMessenger->getMessages();
            $this->_redirect('/contenttypefields/index/id/'.$contentTypeIdent);
            return;
            
        }else{
            // Redirect back to content type fields
            $this->_redirect('/contenttypefields/index/id/'.$contentTypeIdent);
            return;
        }
    }

}