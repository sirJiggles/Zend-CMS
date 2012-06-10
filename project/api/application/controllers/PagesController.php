<?php

/*
 * This is the section of the api that deals with pages in the cms
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class PagesController extends Api_Default
{
    // Create an instance of the pages model
    protected $_pagesModel = '';

    public function init(){
        
        // Set up the Deafult controller 
        parent::init();
        
        $this->_pagesModel = new Application_Model_Pages();
        $this->_helper->viewRenderer->setNoRender(true); 
    }



    public function indexAction()
    {
        if ($this->_isAdmin){
            $data = $this->_pagesModel->getAllPages();
            
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
                $data = $this->_pagesModel->getPageById($this->getRequest()->getParam('id'));
            }
            
            // Get content type by name
            if ($this->getRequest()->getParam('name')){
                $data = $this->_pagesModel->getPageByName($this->getRequest()->getParam('name')); 
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
                    $data = $this->_pagesModel->updatePage(unserialize(base64_decode($_POST['data'])), $_POST['argOne']);
                    break;
                case 'update-assignment':
                    $data = $this->_pagesModel->updatePageAssignment(unserialize(base64_decode($_POST['data'])), $_POST['argOne'], $_POST['argTwo']);
                    break;
                case 'add':
                    $data = $this->_pagesModel->addPage(unserialize(base64_decode($_POST['data'])));
                    break;
                case 'remove':
                    $data = $this->_pagesModel->removePage(unserialize(base64_decode($_POST['data'])));
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