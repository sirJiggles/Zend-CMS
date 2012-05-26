<?php

/*
 * This is where users can assign content to pages based on the content types allowed
 * within the template
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class IndexController extends Cms_Controllers_Default
{

    public function init()
    {
        parent::init();
        
    }
    
    
   /*
     * This is the action for showing all of the pages in the system
     */
    public function indexAction()
    {
        $this->view->pageTitle = 'Jiggly CMS';
        
        // Get all of the pages from the system
        $pages = $this->getFromApi('/pages');
        
        if ($pages !== null){
            
            $this->view->pages = $pages;
            
            // Get all content types from the API to reduce the amount of calls
            $contentTypes = $this->getFromApi('contenttypes');

            $this->view->contentTypes = $contentTypes;
 
            
        }
        
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        
    }
    
    /*
     * This is where users can add pages to the system
     */
    public function addAction(){
        
        
        $this->view->pageTitle = 'Add Page';
        
        // Get all the templates form the API
        $templates = $this->getFromApi('/templates');
        
        // Get an instance of the page form
        $pageForm = new Application_Form_PageForm();
        $pageForm->setValues($templates);
        $pageForm->startForm();
        
        $pageForm->setElementDecorators($this->_formDecorators);
        
        // Send the form to the view
        $this->view->pageForm = $pageForm;
        
        // Add the template based on the form post
        if ($this->getRequest()->isPost()){
           
            // Check if the form data is valid
            if ($pageForm->isValid($_POST)) {
                
                // Run the add template function response from the api
                $addAction = $this->postToApi('/pages', 'add', $pageForm->getValues());
                
                // Error checking
                if ($addAction != 1){
                    
                    if ($addAction == 'Name Taken'){
                        $this->_helper->flashMessenger->addMessage('That page name is already taken, please try again');
                    }else{
                        $this->_helper->flashMessenger->addMessage('Could not add page, please try again');
                    }
                    $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();

                }else{
                     // Set the flash message
                    $this->_helper->flashMessenger->addMessage('Page added');
                    $this->view->messages = $this->_helper->flashMessenger->getMessages();
                    $this->_redirect('/');
                    return;
                }
                
                return;
            }
        }
        
    }
    
    public function editAction(){
        
        $this->view->pageTitle = 'Edit Page';
        
        // First check to make sure we got the id correctly for the template
        $id = $this->getRequest()->getParam('id');
        
        if (!isset($id) || !is_numeric($id)){
            $this->_helper->flashMessenger->addMessage('Could not edit template due to lack of ID');
            $this->_redirect('/templates');
            return;
        }
        
        $currentTemplate = $this->getFromApi('/templates/'.$id);
        
        if ($currentTemplate === null){
            $this->_helper->flashMessenger->addMessage('Could not get current template from API');
            $this->_redirect('/templates');
            return;
        }
        
        $files = $this->_getFiles();
        
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
        
       
        // Check if post
        if ($this->getRequest()->isPost()){
                
            // Check if the form data is valid
            if ($templateForm->isValid($_POST)) {
                
                // attempt to update content via API
                $updateAttempt = $this->postToApi('/templates', 'update',  $templateForm->getValues(), $currentTemplate->id);
                
                // check on status of update
                if ($updateAttempt != 1){
                    if ($updateAttempt == 'Name Taken'){
                        $this->_helper->flashMessenger->addMessage('That name is already taken, please try again');
                    }else{
                        $this->_helper->flashMessenger->addMessage('Unable to update template via the API');
                    }
                    $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
                }else{
                    $this->_helper->flashMessenger->addMessage('template updated');
                    $this->_redirect('/templates');
                    return;
                }

            }       
        }
        
        // Sort the content_types before adding it back to the form
        $currentData = unserialize($currentTemplate->content_types);
        $newFormData = array();
        foreach ($currentData as $key => $val){
            $newFormData['content_'.$key] = $val[0];
        }
        $newFormData['name'] = $currentTemplate->name;
        $newFormData['file'] = $currentTemplate->file;
        
        // add template data back to the form
        $templateForm->populate($newFormData);
        
        // send the form to the view
        $this->view->templateForm = $templateForm;
        
    }
    
   
    /*
     * This is the view for confirming of the user wants to remove a template
     */
    public function removeConfirmAction(){
        
        $this->view->pageTitle = 'Remove Template';
        
        // Get the content by id
        $id = $this->getRequest()->getParam('id');
        
        if (!isset($id) || !is_numeric($id)){
            $this->_helper->flashMessenger->addMessage('You must pass a valid template id');
            $this->_redirect('/templates');
            return;
        }
        
        // Get the content from the api based on the id
        $template = $this->getFromApi('/templates/'.$id);
        
        if ($template === null){
            $this->_helper->flashMessenger->addMessage('Unable to find template in API');
            $this->_redirect('/templates');
            return;
        }
        
        if ($this->_isMobile){
            $this->_helper->layout->setLayout('dialog-mobile');
        }else{
            $this->_helper->layout->setLayout('dialog');
        }

        $this->view->template = $template;
    }
    
    
    /*
     * This is the actual process of removing a template from the system
     */
    public function removeAction(){
        
        // get the id param passed
        $id = $this->getRequest()->getParam('id');
        
        // Sanity check the param
        if (!isset($id) || !is_numeric($id)){
            $this->_helper->flashMessenger->addMessage('You must pass a valid id to remove a template');
            $this->_redirect('/templates');
            return;
        }
        
        // Attempt to remove the content from the api
        $removeAction = $this->postToApi('/templates', 'remove', $id);

        if ($removeAction == 1){
            $this->_helper->flashMessenger->addMessage('Template removed from the system');
        }else{
            $this->_helper->flashMessenger->addMessage('Could not find the template to remove');
        }
        $this->_redirect('/templates');
        return;
            

    }
    
    public function _getFiles(){
        
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
        
        return $files;
        
    }



}

