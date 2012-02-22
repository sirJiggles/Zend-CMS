<?php

/*
 * This is the model with all of the users functions inside it
 * (connects to users table)
 * 
 * Gareth Fuller
 */

class Application_Model_User extends Zend_Db_Table{
    
    protected $_name = 'users';
    
    public function getAllUsers(){
        return $this->fetchAll();
    }
}