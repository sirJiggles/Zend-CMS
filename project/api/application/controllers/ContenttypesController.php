<?php

/*
 * This is the contenttypes controller, only admin users have access to the
 * datatypes content
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class ContenttypesController extends Api_Default
{
    
    protected $_contentTypesModel = '';

    public function init(){
        
        // Set up the Deafult controller 
        parent::init();
        
        $this->_contentTypesModel = new Application_Model_ContentTypes();
        $this->_helper->viewRenderer->setNoRender(true); 
    }



    public function indexAction()
    {
        if ($this->_isAdmin){
            $data = $this->_contentTypesModel->getAllContentTypes();
            
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
            
            // Try Getting the Content Type By Id
            if ($this->getRequest()->getParam('id')){
                $data = $this->_contentTypesModel->getContentTypeById($this->getRequest()->getParam('id'));
            }
            
            // Get content type by name
            if ($this->getRequest()->getParam('name')){
                $data = $this->_contentTypesModel->getContentTypeByName($this->getRequest()->getParam('name')); 
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
                    $data = $this->_contentTypesModel->updateContentType(unserialize(base64_decode($_POST['data'])), $_POST['argOne']);
                    break;
                case 'add':
                    $data = $this->_contentTypesModel->addContentType(unserialize(base64_decode($_POST['data'])));
                    break;
                case 'remove':
                    $data = $this->_contentTypesModel->removeContentType(unserialize(base64_decode($_POST['data'])));
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