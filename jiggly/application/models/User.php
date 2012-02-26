<?php

/*
 * This is the model with all of the users functions inside it
 * (connects to users table)
 * 
 * Gareth Fuller
 */

class Application_Model_User extends Zend_Db_Table{
    
    protected $_name = 'users';
    
    /*
     * Simple function to get all users from the table
     * 
     * @return Zend_Db_Table $users
     */
    public function getAllUsers(){
        return $this->fetchAll();
    }
    
    
    /*
     * Function to get a user by an Id from 
     * the users table
     * 
     * @param int $id
     * @return Zend_Db_Table $user
     */
    public function getUserById($id){
        
        try {
            $selectStatememt = $this->select()
                                    ->where('id = ?', $id);
            $row = $this->fetchRow($selectStatememt);
           
            return $row;
            
        } catch (Exception $e) {
            echo 'Unable to getUserById: ',  $e->getMessage(), "\n";
        }
        
    }
    
}