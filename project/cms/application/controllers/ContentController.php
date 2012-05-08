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
        
        $contentTypes = $this->getFromApi('/datatypes');
        
        if ($contentTypes != null){
            $this->view->contentTypes = $contentTypes;
        }
        
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        
    }
    
    /*
     * This is where content actually gets added into the cms ... 
     */
    public function addAction(){
        
        // First we need to make sure they sent a alid if for the content type
        $id = $this->getRequest()->getParam('id');
        
        // Handle if the id not passed correctly
        if (!isset($id) || !is_numeric($id)){
            $this->_helper->flashMessenger->addMessage('Unable to process add content without a content type');
            $this->_redirect('/content');
            return;
        }
 
        // Get content type data from the API
        $contentType = $this->getFromApi('/datatypes/'.$id);
       
        // handle cant load from API 
        if ($contentType === null){
            $this->_helper->flashMessenger->addMessage('Unable to load content type from API');
            $this->_redirect('/content');
            return;
        }
        
        // Get the content fields based on the content type we just got from the API
        $contentFields = $this->getFromApi('/datatypefields/datatype/'.$contentType->id);
        
        
        
        // Check to make sure we have the api values correctly from the api that is
        if ($contentFields === null){
            $this->_helper->flashMessenger->addMessage('Unable to load content type fields from API');
            $this->_redirect('/content');
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
                    $this->_helper->flashMessenger->addMessage('Could not add content, please try again');
                    $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();

                }else{
                     // Set the flash message
                    $this->_helper->flashMessenger->addMessage('Content added to the system');
                    $this->view->messages = $this->_helper->flashMessenger->getMessages();
                    $this->_redirect('/content');
                    return;
                }
                return;
            }
        }
        

        
    }
}