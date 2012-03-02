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
            throw new Exception('Unable to getUserById in User model: '.$e->getMessage());
        }
        
    }
    
    
    
    /*
     * Function to update users based on the post data
     * 
     * @param array $userData
     * @param array $userId
     * 
     */
    public function updateUser($formData, $userId){
        
        try {
            
            if (is_array($formData) && is_numeric($userId)){
                // Unset the csrf token and password repeat from the data array
                unset($formData['csrf_token']);
                unset($formData['password_repeat']);

                /*
                 * Apply encoding to the user password if the password is set
                 * If the user password id not set, ignore updating
                 */
                if ($formData['password'] != ''){
                    $formData['password'] = sha1($formData['password'].'34idnTgs98');
                }else{
                    unset($formData['password']);
                }
                
                $where = $this->getAdapter()->quoteInto('id = ?', $userId);

                $this->update($formData, $where);
            }else{
                throw new Exception('Incorrect variable types passed to updateUser: expecting array and int');
            }
            
        }catch (Exception $e) {
            throw new Exception('Unable to updateUser in User model: '.$e->getMessage());
        }
        
    }
    
}