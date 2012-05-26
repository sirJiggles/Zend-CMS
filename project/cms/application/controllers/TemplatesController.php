<?php

/*
 * This is where users can add a templates to the system and assign content
 * types to the template
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class TemplatesController extends Cms_Controllers_Default
{
    
    /*
     * Init function for the controller 
     */
    public function init(){
        parent::init();
    }


    /*
     * This is the action for showing the users the current templates in the
     * system
     */
    public function indexAction()
    {
        $this->view->pageTitle = 'Templates';
        
        // Get all of the templates from the system
        $templates = $this->getFromApi('/templates');
        
        if ($templates !== null){
            
            $this->view->templates = $templates;
        }
        
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        
    }
    
    /*
     * This is where users can add templtes to the system
     */
    public function addAction(){
        
        $this->view->pageTitle = 'Add Template';
        
        // Get a list of all the files in the template directory
        $directory = realpath($_SERVER['DOCUMENT_ROOT'].'/../cms/templates');

        $handler = opendir($directory);
        $files = array();

        // open directory and walk through the filenames
        while ($file = readdir($handler)) {
            // if file isn't this directory or its parent, add it to the results
            if ($file != "." && $file != "..") {
                // check to make sure not file with . at start
                if (substr($file, 0, 1) != "."){
                    $files[] = $file;
                }
            }
        }
        // tidy up: close the handler
        closedir($handler);
        
        // Thow message about not being able to add templates until there are
        // some template files in the directory
        if (!isset($files[0]) || $files[0] == ''){
            $this->_helper->flashMessenger->addMessage('No template files in the template directory');
            $this->view->messages = $this->_helper->flashMessenger->getMessages();
            return;
        }
        
        $contentTypes = $this->getFromApi('/contenttypes');
        
        if ($contentTypes === null){
            $this->_helper->flashMessenger->addMessage('No content types defined in the system');
            $this->view->messages = $this->_helper->flashMessenger->getMessages();
            return;
        }
        
        // Get an instance of the template form
        $templateForm = new Application_Form_TemplateForm();
        $templateForm->setValues($files, $contentTypes);
        $templateForm->startForm();
        
        $templateForm->setElementDecorators($this->_formDecorators);
        
        // Send the form to the view
        $this->view->templateForm = $templateForm;
        
        // Add the template based on the form post
        if ($this->getRequest()->isPost()){
           
            // Check if the form data is valid
            if ($templateForm->isValid($_POST)) {
                
                // Run the add template function response from the api
                $addAction = $this->postToApi('/templates', 'add', $templateForm->getValues());
                
                var_dump($addAction);
                
                // Error checking
                if ($addAction != 1){
                    
                    if ($addAction == 'Name Taken'){
                        $this->_helper->flashMessenger->addMessage('That name is already taken, please try again');
                    }else{
                        $this->_helper->flashMessenger->addMessage('Could not add template, please try again');
                    }
                    $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();

                }else{
                     // Set the flash message
                    $this->_helper->flashMessenger->addMessage('Template added to the system');
                    $this->view->messages = $this->_helper->flashMessenger->getMessages();
                    $this->_redirect('/templates');
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
            $this->_redirect('/');
            return;
        }
        
        // Try to get the current content from the API
        $currentContent = $this->getFromApi('/content/'.$id);
       
        // handle cant load from API 
        if ($currentContent === null){
            $this->_helper->flashMessenger->addMessage('Unable to load content from API');
            $this->_redirect('/');
            return;
        }
        
        // Based on the current content we need to know the fields for this content
        // so we will now try get the content fields from the API
        $contentFields = $this->getFromApi('/datatypefields/datatype/'.$currentContent->content_type);
        
        // Check to make sure we have the api values correctly from the api that is
        if ($contentFields === null){
            $this->_helper->flashMessenger->addMessage('Unable to load content type fields from API');
            $this->_redirect('/');
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
                    $this->_redirect('/');
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
            $this->_redirect('/');
            return;
        }
        
        // Get the content from the api based on the id
        $content = $this->getFromApi('/content/'.$id);
        
        if ($content === null){
            $this->_helper->flashMessenger->addMessage('Unable to find content in API');
            $this->_redirect('/');
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
            $this->_redirect('/');
            return;
        }
        
        // Attempt to remove the content from the api
        $removeAction = $this->postToApi('/content', 'remove', $id);

        if ($removeAction == 1){
            $this->_helper->flashMessenger->addMessage('Content removed from the system');
        }else{
            $this->_helper->flashMessenger->addMessage('Could not find the content to remove');
        }
        $this->_redirect('/');
        return;
            

    }
    
    
    
}