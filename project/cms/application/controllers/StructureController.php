<?php

/*
 * This controller performs one task, save the page structure to the API
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class StructureController extends Cms_Controllers_Default
{
    
    /*
     * Init function for the controller 
     */
    public function init(){
        parent::init();
    }


    /*
     * This is where we save the structure to the API
     */
    public function indexAction()
    {
        // Check for the value of the structure query string
        $structureString = $this->getRequest()->getParam('structure');

        if (!isset($structureString) || $structureString == ''){
            return;
        }
        
        // Make the post to the API
        $editAction = $this->postToApi('/structure', 'update', $structureString);
        
        
    }
    

}