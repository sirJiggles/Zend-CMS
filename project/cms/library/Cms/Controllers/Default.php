<?php

/*
 * This is the default controller that all controllers in the application use 
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Plugins
 */

class Cms_Controllers_Default extends Zend_Controller_Action
{
    // Hold the API client here ... and don't let go
    protected $_client = '';
    protected $_apiKey = '';
    
    public $_isMobile = false;
    
    public function init(){
        
        // Get the system API key from the API model 
        $apiModel = new Application_Model_Api();
        $this->_apiKey = $apiModel->getApiKey();
        
        // Have to define the API Urls here as registery values cause phpunit to fail
        switch(APPLICATION_ENV){
            case 'development':
                $apiUrl = 'http://api.jiggly.dev/';
                break;
            case 'staging':
                break;
            case 'production':
            default:
                $apiUrl = 'http://api.jigglycms.com/';
                break;
        }
        
        // Create the Zend Rest Client 
        $this->_client = new Zend_Rest_Client($apiUrl);
        
        
        // Here we set the global "is mobile" flag its just based on mobile browsers
        $mobileBrowsers = array(
            'Android\sWebkit\sBrowser',
            'BlackBerry',
            'Blazer',
            'Bolt',
            'Browser\sfor\sS60',
            'Doris',
            'Dorothy',
            'Fennec',
            'Go\sBrowser',
            'IE\sMobile',
            'Iris',
            'Maemo\sBrowser',
            'MIB',
            'Minimo',
            'NetFront',
            'Opera\sMini',
            'Opera\sMobile',
            'SEMC-Browser',
            'Skyfire',
            'TeaShark',
            'Teleca-Obigo',
            'uZard\sWeb', 
        );
        
        $userAgentString = $_SERVER['HTTP_USER_AGENT'];
        
        $userAgentString = 'Maemo Browser';
        
        foreach($mobileBrowsers as $browser){
            //Run a regular expression to try match the name of the browser
            preg_match('/'.$browser.'/i', $userAgentString, $match);
            if (isset($match[0][0]) && $match[0][0] != ''){
                $this->_isMobile = true;
                break;
            }
        }
        

    }
    
    
    /*
     * Handle get requests to the API
     * 
     * @param string $resource
     * @return mixed $data
     */
    public function getFromApi($resource, $format = 'json'){
        try{
            if (isset($resource) && is_string($resource)){
                
                $data = $this->_client->restGet($resource, 
                                                array('key' => $this->_apiKey, 
                                                    'format' => $format))
                                      ->getBody();
                
                $data = $this->formatResonse($data, $format);
                
                return $data;
            }
        } catch (Exception $e) {
            echo 'Unable to get resource from the API check default controller: '.$e->getMessage();
        }
    }
    
    /*
     * Handle posting to API
     * 
     * @param string $resource
     * @param string $operation
     * @param array $postData
     * (all other params are optional)
     * @return mixed $response
     */
    public function postToApi($resource, $operation, $data, $argOne = null, $argTwo = null, $argThree = null, $argFour = null){
        
        // Sanity checks 
        if (!isset($resource) || !is_string($resource)){
            return false;
        }
        if (!isset($operation) || !is_string($operation)){
            return false;
        }
        if (!isset($data)){
            return false;
        }
        
        try{
            
            $arguments = array('key' => $this->_apiKey,
                                'operation' => $operation,
                                'data' => base64_encode(serialize($data)));

            // Add some arguments if they are needed
            if ($argOne !== null){ 
                $arguments['argOne'] = $argOne;
            }
            if ($argTwo !== null){ 
                $arguments['argTwo'] = $argTwo;
            }
            if ($argThree !== null){ 
                $arguments['argThree'] = $argThree;
            }
            if ($argFour !== null){ 
                $arguments['argFour'] = $argFour;
            }


            $response = $this->_client->restPost($resource, $arguments)->getBody();
            
            return $response;

        } catch (Exception $e) {
            echo 'Unable to put resource to the API check default controller: '.$e->getMessage();
        }
    }
    
    
    /*
     * Function for formatting responses from the API
     * 
     * @param string $data
     * @param string $format
     * @return mixed $formattedData
     */
    public function formatResonse($data, $format){
        switch($format){
            case 'array':
                $returnData = unserialize($data);
                break;
            case 'json':
            default:
                $returnData = json_decode($data);
                break;
        }
        return $returnData;
    }
    
    
    
    
}