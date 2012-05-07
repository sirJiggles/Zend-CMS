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
        $contentType = $this->getFromApi('/datatypes/'.$id, 'array');

        // handle cant load from API 
        if ($contentType == null){
            $this->_helper->flashMessenger->addMessage('Unable to load content type from API');
            $this->_redirect('/content');
            return;
        }
        
        

        
    }
}