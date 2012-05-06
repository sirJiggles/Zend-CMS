<?php

/*
 * This is the datatype fields controller, only admin users have access to the
 * datatype fields content
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class DatatypefieldsController extends Api_Default
{
    
    protected $_dataTypeFieldsModel = '';

    public function init(){
        
        // Set up the Deafult controller 
        parent::init();
       
        $this->_dataTypeFieldsModel = new Application_Model_DataTypeFields();
        $this->_helper->viewRenderer->setNoRender(true); 
    }



    public function indexAction()
    {
        if ($this->_isAdmin){
            $data = $this->_dataTypeFieldsModel->getAllDataTypeFields();
            
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
            
            // Try Getting the Content Type By Id
            if ($this->getRequest()->getParam('id')){
                $data = $this->_dataTypeFieldsModel->getContentTypeFieldById($this->getRequest()->getParam('id'));
            }
            
            // Get content type by name
            if ($this->getRequest()->getParam('name')){
                $data = $this->_dataTypeFieldsModel->getContentFieldByName($this->getRequest()->getParam('name')); 
            }
            
            // Get the content fields for the content type parsed
            if ($this->getRequest()->getParam('datatype')){
                $data = $this->_dataTypeFieldsModel->getDataFieldsForDataType($this->getRequest()->getParam('datatype')); 
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
                    $data = $this->_dataTypeFieldsModel->updateContentTypeField(unserialize(base64_decode($_POST['data'])), $_POST['argOne']);
                    break;
                case 'add':
                    $data = $this->_dataTypeFieldsModel->addContentTypeField(unserialize(base64_decode($_POST['data'])));
                    break;
                case 'remove':
                    $data = $this->_dataTypeFieldsModel->removeContentTypeField(unserialize(base64_decode($_POST['data'])));
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