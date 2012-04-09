<?php
/*
 * This is the plugin that handles the API requests
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Plugins
 */

class RestAuth_Plugin extends Zend_Controller_Plugin_Abstract{
    
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $apiKey = $request->getParam('key');
        
        Zend_Debug::dump($request);
        
        // Get all the API keys from the database
        $apiModel = new Application_Model_Api();
        $keys = $apiModel->getKeys();
        
        $foundKey = false;
        foreach ($keys as $key){
            if ($apiKey == $key['key']){
                $foundKey = true;
                break;
            }
        }
        
        if(!$foundKey){
            $this->getResponse()
                ->setHttpResponseCode(403)
                ->appendBody("Invalid API Key\n");
            
            $request->setModuleName('default')
                        ->setControllerName('error')
                        ->setActionName('access')
                        ->setDispatched(true);

        }

    }
}