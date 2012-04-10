<?php
/*
 * This is the default controller for the API that handles response data
 * and how it is handled and so on
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Plugins
 */

abstract class Api_Default extends Zend_Controller_Action{
    
    protected $_apiUsers = '';
    public $_isAdmin = false;
   
    
    public function init(){
        // Get instance of the API model
        $apiModel = new Application_Model_Api();
        // Get all of the API users
        $this->_apiUsers = $apiModel->getKeys();
        // Check if admin
        $this->checkIfAdmin();
    }
    
    
    /*
     * This function is used to check if the user is an admin user
     */
    public function checkIfAdmin(){
        
        $apiKey = $this->getRequest()->getParam('key');

        $foundUser = null;
        
        foreach ($this->_apiUsers as $user){
            if ($apiKey == $user['key']){
                $foundUser = $user;
                break;
            }
        }
        if ($foundUser !== null){
            if ($foundUser['type'] == 1){
                $this->_isAdmin = true;
            }else{
                $this->_isAdmin = false;
            }
        }

    }
    
    /*
     * This fucntion handles returning the data from the API
     * 
     * @param stdClassObj $data
     */
    public function returnData($data){
        
        // Check if the user of the API wants to return the content
        // in a different way
        $dataFormat = 'json';
        
        if ($this->getRequest()->getParam('format')){
            $dataFormat = $this->getRequest()->getParam('format');
        }
        
        switch($dataFormat){
            case 'array':
                $data = $this->formatData($data, 'array');
                break;
            case 'json':
            default:
                $data = $this->formatData($data, 'json');
                break;
        }
        
        if ($data !== null){
            $this->getResponse()
            ->setHttpResponseCode(200)
            ->appendBody($data);
        }else{
            $this->getResponse()
            ->setHttpResponseCode(200)
            ->appendBody('No Content Found');
        }
                

    }
    
    /*
     * This is the function that formats the data that the API outputs
     * 
     * @param object $data
     * @param string $format
     * @return mixed $formattedData
     */
    public function formatData($data, $format){
        if ($data !== null){
            switch($format){
                case 'array':
                     $formattedData = serialize($data->toArray());
                    break;
                case 'json':
                default:
                    $formattedData = json_encode($data->toArray());
                    break;
            }
            return $formattedData;
        }
    }
    
    
}