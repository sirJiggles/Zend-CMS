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
        /* Initialize action controller here */
    }
    
    
    /*
     * Show all pages in the system in a tree like structure system
     */
    public function indexAction()
    {
        // Show any flash messages
        $this->view->messages = $this->_helper->flashMessenger->getMessages();
        
        
    }


}

