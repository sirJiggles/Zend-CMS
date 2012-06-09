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
            
            // Get all content types from the API to reduce the amount of calls
            $contentTypes = $this->getFromApi('contenttypes');
            
            // Get the page structure from the API and reshuffle the pages
            $structure = $this->getFromApi('/structure');
            $structure = unserialize($structure->structure);
            
            // Break them down into pages
            $structurePages = explode(':', $structure);
            
            // Remove the last one
            array_pop($structurePages);
            
            
            // Should not rly do html in the view, i know i know but there is enough logic to justify it ...
            $finalString = '<ul id="pages">';
            
            $i = 0;
            $ulOpen = false;
            
            foreach($structurePages as $structurePage){
                
                $partsCurrent = explode('-', $structurePage);
                $currentLevel = $partsCurrent[0];
                $pageId = $partsCurrent[1];
                
                $nextLevel = '';
                
                if (isset($structurePages[$i +1])){
                    $partsNext = explode('-', $structurePages[$i +1]);
                    $nextLevel = $partsNext[0];
                }
                
                $element = $this->_generatePageElement($pages, $pageId, $contentTypes);
                
                if (is_string($element)){
                    
                    // Add element
                    $finalString .= '<li class="page-item">';
                    $finalString .= $element;
                    
                    // Work out if sub pages next
                    if ($nextLevel != ''){
                        if ($nextLevel > $currentLevel){
                            $finalString .= '<ul class="sortable">';
                            $ulOpen = true;
                        }elseif($nextLevel == $currentLevel){
                            $finalString .= '</li>';
                        }
                        else{
                            $finalString .= '</ul></li>';
                        }
                    }

                }
               
                
                $i ++;
                
            } // End for each struture page
            
            $finalString .= '</ul>';
            
            $this->view->pageListString = $finalString;
        
        }// End if pages
        
        
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        
    }
    
    /*
     * This is where users can add pages to the system
     */
    public function addAction(){
        
        
        $this->view->pageTitle = 'Add Page';
        
        // Get all the templates form the API
        $templates = $this->getFromApi('/templates');
        
        if ($templates === null){
            $this->_helper->flashMessenger->addMessage('No templates defined in the system');
            $this->view->messages = $this->_helper->flashMessenger->getMessages();
            $this->_redirect('/');
            return;
        }
        
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
    
    /*
     * This is where users can actually assign content to content slots
     */
    public function editassignmentAction(){
        
        $this->view->pageTitle = 'Edit Content Assignment';
        
        // First check to make sure we got the id correctly for the page
        $pageId = $this->getRequest()->getParam('page');
        
        // The type id is wo we know whcih item they have clicked on 0 for first 1 for second etc
        $slot = $this->getRequest()->getParam('id');
        
        if (!isset($pageId) || !is_numeric($pageId)){
            $this->_helper->flashMessenger->addMessage('Could not edit page assignment due to lack of page id');
            $this->_redirect('/');
            return;
        }
        
        if (!isset($slot) || !is_numeric($slot)){
            $this->_helper->flashMessenger->addMessage('Could not edit page assignment due to lack of id');
            $this->_redirect('/');
            return;
        }
        
        $currentPage = $this->getFromApi('/pages/'.$pageId);
        
        if ($currentPage === null){
            $this->_helper->flashMessenger->addMessage('Could not get current page from API');
            $this->_redirect('/');
            return;
        }
        
        // Work out based on the type and the page what content is availible to them
        $contentAssignment = unserialize($currentPage->content_assigned);
        $currentItem = $contentAssignment[$slot];
        $currentActive = $contentAssignment[$slot]['value'];
        $contentTypeId = $currentItem['type'];
        
        
        // Nw get all content for content type from system
        $content = $this->getFromApi('/content/type/'.$contentTypeId);

        // Get an instance of the edit assignment form
        $assignmentForm = new Application_Form_ContentAssignmentForm();
        $assignmentForm->setValues($content, $currentActive);
        $assignmentForm->startForm();
        
        // Send the content type id to the view
        $this->view->typeId = $contentTypeId;
        
       
        // Check if post
        if ($this->getRequest()->isPost()){
                
            // Check if the form data is valid
            if ($assignmentForm->isValid($_POST)) {
                
                // attempt to update content via API
                $updateAttempt = $this->postToApi('/pages', 'update-assignment',  $assignmentForm->getValues(), $currentPage->id, $slot);
                
                // check on status of update
                if ($updateAttempt != 1){
                    $this->_helper->flashMessenger->addMessage('Unable to update page assignment via the API');
                    $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
                    $this->_redirect('/');
                    return;
                }else{
                    $this->_helper->flashMessenger->addMessage('content assignment updated');
                    $this->_redirect('/');
                    return;
                }

            }       
        }
        
        // Sort the content_assigned before adding it back to the form
        /*$currentData = unserialize($page->content_assigned);
        $newFormData = array();
        foreach ($currentData as $map){
            $newFormData['content_'.$key] = $val[0];
        }
        $newFormData['name'] = $currentTemplate->name;
        $newFormData['file'] = $currentTemplate->file;
        
        // add template data back to the form
        $assignmentForm->populate($newFormData);*/
        
        // send the form to the view
        $this->view->assignmentForm = $assignmentForm;
        
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
    
    /*
     * This fucntion is used to get all the files in the templates directory
     */
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
    
    /*
     * This function takes the page id from the struture array and gives us the html
     * for the page element in the list
     * 
     * As a note when we get the content map the format of the dat returned is as follows 
     * gives us something like 
     * 
     * array[0] = array('type' = 7, 'value' = 0)
     *      [1] = array('type' = 7, 'value' = 0)
     *      [2] = array('type' = 3, 'value' = 0);
     * 
     * where type is the type of content and value is the content id
     * assigned to that type for this page (based on the template)
     * 
     * @param object $pages
     * @param int $pageId
     * @param object $contenTypes
     * @return string $element
     */
    public function _generatePageElement($pages, $pageId, $contentTypes){
        // First generate the page element, for this we will need the page object by this ID
        $pageFound = '';
        foreach($pages as $page){
            if ($page->id == $pageId){
                $pageFound = $page;
                break;
            }
        }

        // Should never happen
        if ($pageFound == ''){
            return false;
        }

        
        $element = '<span class="item-wrapper" id="'. $pageFound->id .'">';
        $element .=     '<div class="page-level-controlls">';
        $element .=         '<a class="indent" href="#">in</a> | <a class="outdent" href="#">out</a> | <a class="content-button-toggle" href="#" title="content assignment">Content</a>';                    
        $element .=     '</div>';
        $element .=     '<h4>'.$pageFound->name.'</h4>';

        $currentMapping = unserialize($pageFound->content_assigned);

        $i = 0;
        $element .=     '<ul class="content-buttons">';

        foreach($currentMapping as $map){
            $element .=     '<li class="lock">';
            $element .=         '<span>';

            $name = '';

            // get the name of the content type
            foreach($contentTypes as $contentType){
                if ($contentType->id == $map['type']){
                    $name = $contentType->name;
                    break;
                }
            }
            if ($name != ''){
                $class = ($map['value'] != 0) ? 'class="content-button active"' : 'class="content-button"';
                $element .= '<a href="index/editassignment/page/'.$pageFound->id.'/id/'.$i.'" title="'.$name.'" '.$class.'>'.$name.'</a>';
            }
            $i ++;
            $element .=     '</span>';
            $element .= '</li>';

        }
        $element .= '</ul>';
        $element .= '</span>';
 
        return $element;
    }


}

