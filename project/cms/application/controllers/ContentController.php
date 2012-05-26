<?php

/*
 * This is where content gets added to the cms!
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class ContentController extends Cms_Controllers_Default
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
        $this->view->pageTitle = 'Add Content';
        
        // Get all of the content types from the API then send the array of types
        // to the view
        
        $contentTypes = $this->getFromApi('/contenttypes');
        
        if ($contentTypes != null){
            $this->view->contentTypes = $contentTypes;
        }
        
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        
    }
    
    // This is where users can manage the content based on the type
    public function manageAction(){

        $type = $this->getRequest()->getParam('type');
        
        if (isset($type)){
            // Get all the content for this type
            $content = $this->getFromApi('/content/type/'.$type);
            
            // Get the content type object so we can get the name of it for display
            $contentType = $this->getFromApi('/contenttypes/'.$type);
            
            // Content type is a must have so check here for it
            if ($contentType === null){
                $this->_helper->flashMessenger->addMessage('Could not get content type data from the API');
                $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
                $this->_redirect('/content');
                return;
            }
            
            // Check if there is no content defined for this type yet, if not display message
            if ($content === null){
                $this->_helper->flashMessenger->addMessage('No Content currently defined');
            }
            
            // send the content data to the view
            $this->view->content = $content;
            
            // send content type data to the view
            $this->view->contentType = $contentType;
            
        }else{
            $this->_helper->flashMessenger->addMessage('Need to parse a content type');
            $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
            $this->_redirect('/content');
            return;
        }

        
         // Show any flash messages
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
    }
    
    /*
     * This is where content actually gets added into the cms ... 
     */
    public function addAction(){
        
        $this->view->pageTitle = 'Add Content';
        
        // First we need to make sure they sent a valid id for the content type
        $id = $this->getRequest()->getParam('id');
        
        // Handle if the id not passed correctly
        if (!isset($id) || !is_numeric($id)){
            $this->_helper->flashMessenger->addMessage('Unable to process add content without a content type');
            $this->_redirect('/content');
            return;
        }
 
        // Get content type data from the API
        $contentType = $this->getFromApi('/contenttypes/'.$id);
       
        // handle cant load from API 
        if ($contentType === null){
            $this->_helper->flashMessenger->addMessage('Unable to load content type from API');
            $this->_redirect('/content/manage/type/'.$id);
            return;
        }
        
        // Get the content fields based on the content type we just got from the API
        $contentFields = $this->getFromApi('/contenttypefields/contenttype/'.$contentType->id);
        
        // Check to make sure we have the api values correctly from the api that is
        if ($contentFields === null){
            $this->_helper->flashMessenger->addMessage('Unable to load content type fields from API');
            $this->_redirect('/content/manage/type/'.$id);
            return;
        }
        
        
        // Get the insert content form (no inputs at this stage)
        $contentForm = new Application_Form_ContentForm();
        $contentForm->setValues($contentFields);
        $contentForm->startForm();
        
        // Add hidden input for the content type ident
        // anoyingly have to validate that this is correct the other end as editors
        // have access to this section and can balls it up if they chnage hidden
        // input values
        $hiddenContentTypeIdField = new Zend_Form_Element_Hidden('content_type');
        $hiddenContentTypeIdField->setValue($contentType->id);  
        $contentForm->addElement($hiddenContentTypeIdField);
        
        $contentForm->setElementDecorators($this->_formDecorators);
        
        
        // Send the form to the view baby
        $this->view->contentForm = $contentForm;
        
        // Add the content based on the form post
        if ($this->getRequest()->isPost()){
           
            // Check if the form data is valid
            if ($contentForm->isValid($_POST)) { 
                // Run the add content function at the api
               
                $addAction = $this->postToApi('/content', 'add', $contentForm->getValues());
                
                // Error checking
                if ($addAction != 1){
                    
                    if ($addAction == 'Ref Taken'){
                        $this->_helper->flashMessenger->addMessage('That ref is already taken, please try again');
                    }else{
                        $this->_helper->flashMessenger->addMessage('Could not add content, please try again');
                    }
                    $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();

                }else{
                     // Set the flash message
                    $this->_helper->flashMessenger->addMessage('Content added to the system');
                    $this->view->messages = $this->_helper->flashMessenger->getMessages();
                    $this->_redirect('/content/manage/type/'.$id);
                    return;
                }
                return;
            }
        }
        
    }
    
    public function editAction(){
        
        $this->view->pageTitle = 'Edit Content';
        
        // First check to make sure we got the id correctly for the content
        $id = $this->getRequest()->getParam('id');
        
        if (!isset($id) || !is_numeric($id)){
            $this->_helper->flashMessenger->addMessage('Could not edit cotent type due to lack of ID');
            $this->_redirect('/content');
            return;
        }
        
        // Try to get the current content from the API
        $currentContent = $this->getFromApi('/content/'.$id);
       
        // handle cant load from API 
        if ($currentContent === null){
            $this->_helper->flashMessenger->addMessage('Unable to load content from API');
            $this->_redirect('/content');
            return;
        }
        
        // Based on the current content we need to know the fields for this content
        // so we will now try get the content fields from the API
        $contentFields = $this->getFromApi('/contenttypefields/contenttype/'.$currentContent->content_type);
        
        // Check to make sure we have the api values correctly from the api that is
        if ($contentFields === null){
            $this->_helper->flashMessenger->addMessage('Unable to load content type fields from API');
            $this->_redirect('/content/manage/type/'.$currentContent->content_type);
            return;
        }
        
        // Get the insert content form (no inputs at this stage)
        $contentForm = new Application_Form_ContentForm();
        $contentForm->setValues($contentFields);
        $contentForm->startForm();
        
        
        // Add hidden input for the content type ident
        // anoyingly have to validate that this is correct the other end as editors
        // have access to this section and can balls it up if they chnage hidden
        // input values
        $hiddenContentTypeIdField = new Zend_Form_Element_Hidden('content_type');
        $hiddenContentTypeIdField->setValue($currentContent->content_type);  
        $contentForm->addElement($hiddenContentTypeIdField);
        
        $contentForm->setElementDecorators($this->_formDecorators);
        
        // Check if post
        if ($this->getRequest()->isPost()){
                
            // Check if the form data is valid
            if ($contentForm->isValid($_POST)) {
                
                // attempt to update content via API
                $updateAttempt = $this->postToApi('/content', 'update',  $contentForm->getValues(), $currentContent->id);
                
                // check on status of update
                if ($updateAttempt != 1){
                    if ($updateAttempt == 'Ref Taken'){
                        $this->_helper->flashMessenger->addMessage('That ref is already taken, please try again');
                    }else{
                        $this->_helper->flashMessenger->addMessage('Unable to update content via the API');
                    }
                    $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
                }else{
                    $this->_helper->flashMessenger->addMessage('content updated');
                    $this->_redirect('/content/manage/type/'.$currentContent->content_type);
                    return;
                }

            }       
        }
        
        // Sort the content before adding it back to the form
        $currentData = unserialize($currentContent->content);
        $contentFormArray = array();
        foreach ($currentData as $key => $val){
            $contentFormArray[$key] = $val[0];
        }
        $contentFormArray['ref'] = $currentContent->ref;
        
        // add content back to the form
        $contentForm->populate($contentFormArray);
        
        // send the form to the view
        $this->view->contentForm = $contentForm;
        
    }
    
   
    /*
     * This is the view for confirming of the user wants to remove some content
     */
    public function removeConfirmAction(){
        
        $this->view->pageTitle = 'Remove Content';
        
        // Get the content by id
        $id = $this->getRequest()->getParam('id');
        
        if (!isset($id) || !is_numeric($id)){
            $this->_helper->flashMessenger->addMessage('You must pass a valid content id');
            $this->_redirect('/content');
            return;
        }
        
        // Get the content from the api based on the id
        $content = $this->getFromApi('/content/'.$id);
        
        if ($content === null){
            $this->_helper->flashMessenger->addMessage('Unable to find content in API');
            $this->_redirect('/content');
            return;
        }
        
        if ($this->_isMobile){
            $this->_helper->layout->setLayout('dialog-mobile');
        }else{
            $this->_helper->layout->setLayout('dialog');
        }

        $this->view->content = $content;
    }
    
    
    /*
     * This is the actual process of removing content from the system
     */
    public function removeAction(){
        
        // get the id param passed
        $id = $this->getRequest()->getParam('id');
        
        // Sanity check the param
        if (!isset($id) || !is_numeric($id)){
            $this->_helper->flashMessenger->addMessage('You must pass a valid id to remove content');
            $this->_redirect('/content');
            return;
        }
        
        $content = $this->getFromApi('/content/'.$id);
        
        if ($content === null){
            $this->_helper->flashMessenger->addMessage('Could not find the content you wish to remove');
            $this->_redirect('/content');
            return;
        }
        
        // Attempt to remove the content from the api
        $removeAction = $this->postToApi('/content', 'remove', $id);

        if ($removeAction == 1){
            $this->_helper->flashMessenger->addMessage('Content removed from the system');
        }else{
            $this->_helper->flashMessenger->addMessage('Could not find the content to remove');
        }
        $this->_redirect('/content/manage/type/'.$content->content_type);
        return;
            

    }
    
    
    
}