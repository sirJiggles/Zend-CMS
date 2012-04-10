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
    
    protected $_settingsModel = '';

    
    /*
     * Init function for the controller 
     */
    public function init(){

        // As we connect to the settings model many times inthis controller we 
        // will create a global instance
        $this->_settingsModel = new Application_Model_Settings();
    }


    /*
     * This is the action that shows the users the form for editing thier
     * site settings in the CMS
     */
    public function indexAction()
    {
        $this->view->pageTitle = 'Site Settings';
        
        $form = new Application_Form_Settings();
        $form->setAction('/settings');
        $form->setMethod('post');

        if ($this->getRequest()->isPost()){

            // Check if the form data is valid
            if ($form->isValid($_POST)) {
                $updateAttempt = $this->_settingsModel->updateSettings($form->getValues());

                if ($updateAttempt){
                    $this->_helper->flashMessenger->addMessage('Site settings updated');
                }else{
                    $this->_helper->flashMessenger->addMessage('Unable to update settings');
                }
                $this->view->messages = $this->_helper->flashMessenger->getMessages();

            }
        }
        
        $currentSettings = $this->_settingsModel->getSettings();
        
        if (!$currentSettings instanceof Zend_Db_Table_Row){
           $this->_helper->flashMessenger->addMessage('Unable to get current settings');
           $this->view->messages = $this->_helper->flashMessenger->getMessages();
           return;
        }
        
        $form->populate($currentSettings->toArray());
        // Send the form to the view
        $this->view->form = $form;
        
        
    }

}