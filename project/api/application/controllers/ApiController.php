<?php

/*
 * This is the controller that deals with sending / recieving API accounts
 * from the api ... go figure
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class ApiController extends Api_Default
{
    
    protected $_apiModel = '';

    public function init(){
        
        // Set up the Deafult controller 
        parent::init();
        
        // As we connect to the user model many times inthis controller we will create a global instance
        $this->_apiModel = new Application_Model_Api();
        $this->_helper->viewRenderer->setNoRender(true); 
    }



    public function indexAction()
    {
        if ($this->_isAdmin){
            $data = $this->_apiModel->getApiUsers();
            
            $this->returnData($data);
            
        }else{
            $this->returnNoAuth();
        }

    }

    public function getAction()
    {

        // If they have an admin api key
        if ($this->_isAdmin){
            
            $data = 'Operation not found';
            
            // Try Getting the api user By Id
            if ($this->getRequest()->getParam('id')){
                if ($this->getRequest()->getParam('id') != 1){ 
                    $data = $this->_apiModel->getUserById($this->getRequest()->getParam('id'));
                }else{
                    $this->getResponse()
                    ->appendBody("You do not have access to this data");
                    return;
                }
            }
            // Get api user by refernce
            if ($this->getRequest()->getParam('ref')){
                $data = $this->_apiModel->getUserByRef($this->getRequest()->getParam('ref')); 
            }

            $this->returnData($data);
            
        }else{
            
           $this->returnNoAuth();
        }
        
        

    }
    
    public function postAction()
    {
        // If they have an admin api key
        if ($this->_isAdmin){
            
            // Work out the type of action they want to perform
            switch($_POST['operation']){
                case 'update':
                    $data = $this->_apiModel->updateUser(unserialize(base64_decode($_POST['data'])), $_POST['argOne']);
                    break;
                case 'add':
                    $data = $this->_apiModel->addUser(unserialize(base64_decode($_POST['data'])));
                    break;
                case 'remove':
                    $data = $this->_apiModel->removeUser(unserialize(base64_decode($_POST['data'])));
                    break;
                
                default:
                    $data = 'Operation not found';
                    break;
            }
            
            $this->returnPostResult($data);
            
        }else{
            $this->returnNoAuth();
        }

    }
    
    public function putAction()
    {
      
    }
    
    public function deleteAction()
    {

    }
    
    

}