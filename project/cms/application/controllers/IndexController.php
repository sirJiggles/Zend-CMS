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
        $contentTypes = $this->getFromApi('/datatypes');
        
        if ($contentTypes === null){
            $this->_helper->flashMessenger->addMessage('Could not get content types from the database');
            $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
            return;
            
        }
        
        // array to hold all of the final content
        $finalContent = array();
        
        // For each content type that we have get all content
        foreach ($contentTypes as $contentType){
            $contentTypeData = $this->getFromApi('/content/type/'.$contentType->id);
            
            // must have data for this conyent type
            if ($contentTypeData !== null){
                $finalContent[$contentType->name][] = $contentTypeData;
            }
        }
        
        //var_dump($finalContent);
        //exit();

        $this->view->content = $finalContent;
         // Show any flash messages
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        
    }


}

