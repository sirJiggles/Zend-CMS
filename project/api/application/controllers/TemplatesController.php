<?php

/*
 * This is the section of the api that deals with getting setting etc the templates
 * in the cms, public users have access to this data
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class TemplatesController extends Api_Default
{
    // Create an instance of the template model
    protected $_templatesModel = '';

    public function init(){
        
        // Set up the Deafult controller 
        parent::init();
        
        $this->_templatesModel = new Application_Model_Templates();
        $this->_helper->viewRenderer->setNoRender(true); 
    }



    public function indexAction()
    {
        if ($this->_isAdmin){
            $data = $this->_templatesModel->getAllTemplates();
            
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
            
            // Try Getting the Template By Id
            if ($this->getRequest()->getParam('id')){
                $data = $this->_templatesModel->getTemplateById($this->getRequest()->getParam('id'));
            }
            
            // Get content type by name
            if ($this->getRequest()->getParam('name')){
                $data = $this->_templatesModel->getTemplateByName($this->getRequest()->getParam('name')); 
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
                    $data = $this->_templatesModel->updateTemplate(unserialize(base64_decode($_POST['data'])), $_POST['argOne']);
                    break;
                case 'add':
                    $data = $this->_templatesModel->addTemplate(unserialize(base64_decode($_POST['data'])));
                    break;
                case 'remove':
                    $data = $this->_templatesModel->removeTemplate(unserialize(base64_decode($_POST['data'])));
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