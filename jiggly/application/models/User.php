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
     * @return Zend_Db_Table_Row $user
     */
    public function getUserById($id){
        
        try {
            $selectStatememt = $this->select()
                                    ->where('id = ?', $id);
            $row = $this->fetchRow($selectStatememt);
           
            return $row;
            
        } catch (Exception $e) {
            echo 'Unable to getUserById in User model: '.$e->getMessage();
        }
        
    }
    
    /*
     * This is the function to remove users from the system
     * 
     * @param int $id
     * @return boolean task status
     */
    public function removeUser($id){
        try {
            $where = $this->getAdapter()->quoteInto('id = ?', $id);
            $result = $this->delete($where);
            return $result;
            
        } catch (Exception $e) {
            echo 'Unable to removeUser in User model: '.$e->getMessage();
        }
    }
    
    
    
    /*
     * Function to update users based on the post data
     * 
     * @param array $userData
     * @param array $userId
     * @return boolean $result
     * 
     */
    public function updateUser($formData, $userId){
        
        try {
            
            if (is_array($formData) && is_numeric($userId)){
                
                /* 
                 * First get all the users and make sure the username they are tring
                 * to does not already exist
                 */
                $currentUsers = $this->fetchAll();
                $nameTaken = false;
                
                foreach ($currentUsers as $currentUser){
                    if ($currentUser['id'] != $userId){
                        // Check if the new username is taken
                        if ($currentUser['username'] == $formData['username']){
                            $nameTaken = true;
                            break;
                        } 
                    }
                }
                
                if (!$nameTaken){
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
                    
                    return true;
                }else{
                    return false;
                }
            }else{
                throw new Exception('Incorrect variable types passed to updateUser: expecting array and int');
            }
            
        }catch (Exception $e) {
            echo 'Unable to updateUser in User model: '.$e->getMessage();
        }
        
    }
    
    /*
     * Function for adding users to the system
     * 
     * @param array $postData
     * @return boolean task result
     * 
     */
    public function addUser($formData){
        try {
            
            if (is_array($formData)){
                
                $currentUsers = $this->fetchAll();
                $nameTaken = false;
                
                foreach ($currentUsers as $currentUser){
                    if ($currentUser['username'] == $formData['username']){
                        $nameTaken = true;
                        break;
                    } 
                }
                
                if (!$nameTaken){
                
                    // Unset the csrf token and password repeat from the data array
                    unset($formData['csrf_token']);
                    unset($formData['password_repeat']);

                    //Apply encoding to the user password if the password 
                    $formData['password'] = sha1($formData['password'].'34idnTgs98');

                    $formData['active'] =  1;

                    // Add database record
                    $newRow = $this->createRow($formData);
                    $newRow->save();
                    
                    return true;
                }else{
                    return false;
                }
                
            }else{
                throw new Exception('Incorrect variable types passed to addUser: expecting array of form data');
            }
            
        }catch (Exception $e) {
            echo 'Unable to addUser in User model: '.$e->getMessage();
        }
    }
    
    
    /*
     * This function gets users from the users table by the email address passed
     * 
     * @param string $emailAddress
     * @return Zend_Db_Table_Row $user
     */
    public function getByEmailAddress($emailAddress){
        
        try {
            if (is_string($emailAddress)){
                
                $selectStatememt = $this->select()
                                    ->where('email_address = ?', $emailAddress);
                
                $row = $this->fetchRow($selectStatememt);

                return $row;
            }else{
                throw new Exception('Unable to fetch user by email address given in User::getByEmailAddress, argument must be string');
            }
            
        } catch (Exception $e) {
            echo 'Unable to removeUser in User model: '.$e->getMessage();
        }
        
        
        
    }
    
    
}