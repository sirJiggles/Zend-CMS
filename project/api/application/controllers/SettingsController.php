<?php

/*
 * This is the settings controller for the API here we handle any requests
 * that are settings orientated in the API
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class SettingsController extends Api_Default
{
    
    protected $_settingsModel = '';

    public function init(){
        
        // Set up the Deafult controller 
        parent::init();
        
        // As we connect to the user model many times inthis controller we will create a global instance
        $this->_settingsModel = new Application_Model_Settings();
        $this->_helper->viewRenderer->setNoRender(true); 
    }



    public function indexAction()
    {
        if ($this->_isAdmin){
            $data = $this->_settingsModel->getSettings();
            
            $this->returnData($data);
            
        }else{
            $this->getResponse()
            ->setHttpResponseCode(403)
            ->appendBody("You do not have access to this data");
        }

    }

    public function getAction()
    {

    }
    
    public function postAction()
    {
        // If they have an admin api key
        if ($this->_isAdmin){
            
            // Work out the type of action they want to perform
            switch($_POST['operation']){
                case 'update':
                    $data = $this->_settingsModel->updateSettings(unserialize($_POST['data']));
                    break;
                default:
                    $data = 'Operation not found';
                    break;
            }
            
            $this->getResponse()
                ->setHttpResponseCode(200)
                ->appendBody($data);
            
        }else{
            $this->getResponse()
            ->setHttpResponseCode(403)
            ->appendBody("You do not have access to this data");
        }

    }
    
    public function putAction()
    {
      
    }
    
    public function deleteAction()
    {

    }
    
    

}