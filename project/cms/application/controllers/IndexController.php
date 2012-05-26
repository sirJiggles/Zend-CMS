<?php

/*
 * As Editors and Admins will both see this page and the main thing they
 * will want to do is ... edit content we will have the index controller 
 * be where they edit content
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
     * Show all pages in the system in a tree like structure system
     */
    public function indexAction()
    {
       
        // First get all the types of data that we can have in the system
        $contentTypes = $this->getFromApi('/contenttypes');
        
        if ($contentTypes === null){
            $this->_helper->flashMessenger->addMessage('Could not get content types from the database');
            $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
            return;
        }
        
        // Send these to the view
        $this->view->contentTypes = $contentTypes;
        
        
        // Here wecheck if the user wants to get content by a content type 
        // Check make sure we have the right params for this page
        
        $filterValue = $this->getRequest()->getParam('filter');
        
        if (isset($filterValue)){
            // Attempt to load the content type based on the filter for
            // validation purposes
             $contentTypeData = $this->getFromApi('/content/type/'.$filterValue);
            
            // must have data for this content type
            if ($contentTypeData === null){
                $this->_helper->flashMessenger->addMessage('Could not filter by that content type');
                $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
            }
            
            // send the content type data to the view
            $this->view->typeData = $contentTypeData;
            
        }

        
         // Show any flash messages
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        
    }



}

