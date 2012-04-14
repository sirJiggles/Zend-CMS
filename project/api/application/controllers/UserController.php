<?php

/*
 * This is the user controller for the API here we handle any requests
 * that are user orientated in the API
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class UserController extends Api_Default
{
    
    protected $_userModel = '';

    public function init(){
        
        // Set up the Deafult controller 
        parent::init();
        
        // As we connect to the user model many times inthis controller we will create a global instance
        $this->_userModel = new Application_Model_User();
        $this->_helper->viewRenderer->setNoRender(true); 
    }



    public function indexAction()
    {
        if ($this->_isAdmin){
            $data = $this->_userModel->getAllUsers();
            
            $this->returnData($data);
            
        }else{
            $this->getResponse()
            ->setHttpResponseCode(403)
            ->appendBody("You do not have access to this data");
        }

    }

    public function getAction()
    {

        // If they have an admin api key
        if ($this->_isAdmin){
            
            $data = 'Operation not found';
            
            // Try Getting the User By Id
            if ($this->getRequest()->getParam('id')){
                $data = $this->_userModel->getUserById($this->getRequest()->getParam('id'));
            }
            
            // Get user by username
            if ($this->getRequest()->getParam('name')){
                $data = $this->_userModel->getUserByUsername($this->getRequest()->getParam('name')); 
            }
            
            // Get user by email address
            if ($this->getRequest()->getParam('email')){
                $data = $this->_userModel->getByEmailAddress($this->getRequest()->getParam('email')); 
            }
            
            
            $this->returnData($data);
            
        }else{
            
            $this->getResponse()
            //->setHttpResponseCode(403)
            ->appendBody("You do not have access to this data");
        }
        
        

    }
    
    public function postAction()
    {
        // If they have an admin api key
        if ($this->_isAdmin){
            
            // Work out the type of action they want to perform
            switch($_POST['operation']){
                case 'update':
                    $data = $this->_userModel->updateUser(unserialize($_POST['data']), $_POST['argOne']);
                    break;
                case 'add':
                    $data = $this->_userModel->addUser(unserialize($_POST['data']));
                    break;
                case 'remove':
                    $data = $this->_userModel->removeUser(unserialize($_POST['data']));
                    break;
                case 'validateHash':
                    $data = $this->_userModel->validateHash(unserialize($_POST['data']), $_POST['argOne']);
                    break;
                case 'updateForgotPassword':
                    $data = $this->_userModel->updateForgotPassword(unserialize($_POST['data']));
                    break;
                case 'updatePasswordHash':
                    $data = $this->_userModel->updateForgotPasswordHash(unserialize($_POST['data']), $_POST['argOne']);
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