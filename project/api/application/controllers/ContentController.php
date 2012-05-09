<?php

/*
 * This is the section of the api that deals with getting setting etc the content
 * in the cms, public users have access to this data
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class ContentController extends Api_Default
{
    // Create an instance of the content model
    protected $_contentModel = '';

    public function init(){
        
        // Set up the Deafult controller 
        parent::init();
        
        $this->_contentModel = new Application_Model_Content();
        $this->_helper->viewRenderer->setNoRender(true); 
    }



    public function indexAction()
    {
        // We wont do anything for this action as we dont want to return ALL
        // of the system data
    }

    public function getAction()
    {

        $data = 'Operation not found';

        // Try Getting the Content by id, can also pass param to get part of content
        if ($this->getRequest()->getParam('id')){

            $data = $this->_contentModel->getContentById($this->getRequest()->getParam('id'));
            
             // If the user wants to break the data down further they can here
            if ($this->getRequest()->getParam('param')){
                
                $requestParam = $this->getRequest()->getParam('param');
                
                // if we have some data
                if ($data !== null){
                    $data = $data->toArray();
                    $content = unserialize($data['content']);
                    if (isset($content[$requestParam])){
                        $returnData = $content[$requestParam];
                    }
                    
                    // turn the data into a class
                    $data = new stdClass();
                    $data->data = $returnData;
                }
            }
        }

        // Get content by content type (returns all news articles for example)
        if ($this->getRequest()->getParam('type')){
            $data = $this->_contentModel->getContentByType($this->getRequest()->getParam('type')); 
        }

        $this->returnData($data);
 
    }
    
    public function postAction()
    {
        
        // If they have an admin api key
        if ($this->_isAdmin){
            
            // Work out the type of action they want to perform
            switch($_POST['operation']){
               
                case 'add':
                    $data = $this->_contentModel->addContent(unserialize(base64_decode($_POST['data'])));
                    break;
                case 'update':
                    $data = $this->_contentModel->updateContent(unserialize(base64_decode($_POST['data'])),$_POST['argOne']);
                    break;
                case 'remove':
                    $data = $this->_contentModel->removeContent(unserialize(base64_decode($_POST['data'])));
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