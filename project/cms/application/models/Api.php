<?php

/*
 * We only ahve one function in here and that gets the admin users api key
 * 
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Models
 */
class Application_Model_Api extends Zend_Db_Table{
    
    protected $_name = 'api';
    
    /*
     * Get all of the api keys form the database
     */
    public function getApiKey(){
        try {
            
            $select = $this->select()->where('id = 1');
            $row = $this->fetchRow($select);
            return $row->key;
            
        }catch (Exception $e) {
            echo 'Unable to get settings in Settings model: '.$e->getMessage();
        }
    }
    
    function __destruct(){
        
    }
    
}