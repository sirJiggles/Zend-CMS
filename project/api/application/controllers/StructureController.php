<?php

/*
 * This is the controller for saving the page structure ... nothing more
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Controllers
 */

class StructureController extends Api_Default
{
    
    protected $_structureModel = '';

    public function init(){
        
        // Set up the Deafult controller 
        parent::init();
        
        // Get a connectio to the model
        $this->_structureModel = new Application_Model_Structure();
        $this->_helper->viewRenderer->setNoRender(true); 
    }


    // This function gets the structure of the pages
    public function indexAction()
    {
        
        $data = $this->_structureModel->getStructure();
        $this->returnData($data);

    }

    public function getAction()
    {
        // Do nothing
    }
    
    public function postAction()
    {

        // Work out the type of action they want to perform
        switch($_POST['operation']){
            case 'update':
                $data = $this->_structureModel->updateStructure(unserialize(base64_decode($_POST['data'])));
                break;
            default:
                $data = 'Operation not found';
                break;
        }

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->appendBody($data);

    }
    
    public function putAction()
    {
      
    }
    
    public function deleteAction()
    {

    }
    
    

}