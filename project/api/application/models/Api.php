<?php

/*
 * This is the mode that handles all of the api settings
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
    public function getKeys(){
        try {
            
            $data = $this->fetchAll();
            
            return $data->toArray();
            
        }catch (Exception $e) {
            echo 'Unable to get settings in Settings model: '.$e->getMessage();
        }
    }
    
}