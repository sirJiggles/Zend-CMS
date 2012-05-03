<?php

/*
 * This is the settings controller, this controller is where the admin of the
 * system can chnage all the details that are relevent to thier site
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class SettingsController extends Cms_Controllers_Default
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
        $this->view->pageTitle = 'Site Settings';
        
        $form = new Application_Form_Settings();
        $form->setMethod('post');
        $form->setElementDecorators($this->_formDecorators);
            
        // Set the active values
        if ($this->_isMobile){
            $form->getElement('theme_path')->setAttrib('placeholder', 'Theme path');
        }

        if ($this->getRequest()->isPost()){

            // Check if the form data is valid
            if ($form->isValid($_POST)) {
                $updateAttempt = $this->postToApi('/settings', 'update', $form->getValues());
                if ($updateAttempt){
                    $this->_helper->flashMessenger->addMessage('Site settings updated');
                }else{
                    $this->_helper->flashMessenger->addMessage('Unable to update settings');
                }
                $this->view->messages = $this->_helper->flashMessenger->getCurrentMessages();
                $this->_helper->flashMessenger->clearCurrentMessages();

            }
        }
        
        $currentSettings = $this->getFromApi('/settings', 'array');
        
        if (!is_array($currentSettings)){
           $this->_helper->flashMessenger->addMessage('Unable to get current settings');
           $this->view->messages = $this->_helper->flashMessenger->getMessages();
           return;
        }
        
        $form->populate($currentSettings);
        // Send the form to the view
        $this->view->form = $form;
        
        
    }

}