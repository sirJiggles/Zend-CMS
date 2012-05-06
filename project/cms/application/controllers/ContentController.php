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
        
        /*
         * Get a list of all the types of content based on the view helpers in 
         * the helpers directory
         */
        $contentTypes = array();
        if ($handle = opendir(APPLICATION_PATH.'/views/helpers/')) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {

                    // Clean the names of the view helpers to create URL's to them
                    $urlString = '';
                    $urlString = str_replace('.php', '', $entry);
                    $urlString = strtolower(str_replace(' ', '-', $urlString));
                    $contentTypes[] = $urlString;

                }
            }
            closedir($handle);
        }
        
        // Send the possible content types to the view
        $this->view->contentTypes = $contentTypes;
        
    }

}