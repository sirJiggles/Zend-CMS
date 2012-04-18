<?php

/*
 * This is the model that handles all of the User specific fucntionality
 * 
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Models
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
     * Function to grab users by the username param
     * 
     * @param string $username
     * @return Zend_Db_Table_Row $user
     */
    public function getUserByUsername($username){
        
        try {
            $selectStatememt = $this->select()
                                    ->where('username = ?', $username);
            $row = $this->fetchRow($selectStatememt);
           
            return $row;
            
        } catch (Exception $e) {
            echo 'Unable to getUserByUsername in User model: '.$e->getMessage();
        }
        
    }
    
    /*
     * Function to get a user by an Id from 
     * the users table
     * 
     * @param int $userId
     * @return Zend_Db_Table_Row $user
     */
    public function getUserById($userId){
        
        try {
            $selectStatememt = $this->select()
                                    ->where('id = ?', $userId);
            $row = $this->fetchRow($selectStatememt);
           
            return $row;
            
        } catch (Exception $e) {
            echo 'Unable to getUserById in User model: '.$e->getMessage();
        }
        
    }
    
    /*
     * This is the function to remove users from the system
     * 
     * @param int $userId
     * @return boolean task status
     */
    public function removeUser($userId){
        try {
            $where = $this->getAdapter()->quoteInto('id = ?', $userId);
            $result = $this->delete($where);
            if ($result){
                return true;
            }else{
                return 'Unable to find user to delete';
            }
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
                 * First get all the users and make sure the username / email they 
                 * are tring to update does not already exist
                 */
                $currentUsers = $this->fetchAll();
                $nameTaken = false;
                $emailTaken = false;
                
                foreach ($currentUsers as $currentUser){
                    if ($currentUser['id'] != $userId){
                        // Check if the new username is taken
                        if ($currentUser['username'] == $formData['username']){
                            $nameTaken = true;
                            break;
                        } 
                        if ($currentUser['email_address'] == $formData['email_address']){
                            $emailTaken = true;
                            break;
                        }
                    }
                }
                
                if (!$nameTaken && !$emailTaken){
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
                    if ($nameTaken){
                        return 'Name Taken';
                    }
                    if ($emailTaken){
                        return 'Email Taken';
                    }
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
                $emailTaken = false;
                
                foreach ($currentUsers as $currentUser){
                    if ($currentUser['username'] == $formData['username']){
                        $nameTaken = true;
                        break;
                    }
                    if ($currentUser['email_address'] == $formData['email_address']){
                        $emailTaken = true;
                        break;
                    }
                    
                }
                
                if (!$nameTaken && !$emailTaken){
                
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
                    if ($nameTaken){
                        return 'Name Taken';
                    }
                    if ($emailTaken){
                        return 'Email Taken';
                    }
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
                
                if ($row instanceof Zend_Db_Table_Row){
                    return $row;
                }else{
                    return false;
                }
                
            }else{
                throw new Exception('Unable to fetch user by email address given in User::getByEmailAddress, argument must be string');
            }
            
        } catch (Exception $e) {
            echo 'Unable to removeUser in User model: '.$e->getMessage();
        }
    }
    
    
    
    /*
     * This function updates the user's forgot password hash 
     * 
     * @param int $userId
     * @param mixed $hash
     */
    public function updateForgotPasswordHash($userId, $hash){
        
         try {
            if (is_numeric($userId)){

                $where = $this->getAdapter()->quoteInto('id = ?', $userId);
                
                $this->update(array('forgot_password_hash' => $hash, 'active' => 0), $where);
                
            }else{
                throw new Exception('Unable to update forgotten pasword hash in  User::updateForgotPasswordHash, userId must be int');
            }
            
        } catch (Exception $e) {
            echo 'Unable to updateForgotPasswordHash in User model: '.$e->getMessage();
        }
    }
    
    /*
     * This function validates a hash used for the remember password 
     * functionality.
     * 
     * @param int $userId
     * @param int $hash
     * @return boolean $result
     */
    public function validateHash($userId, $hash){
        try {
            
            if (is_numeric($userId) && is_numeric($hash)){

                $selectStatememt = $this->select()
                                    ->where('id = ?', $userId)
                                    ->where('forgot_password_hash = ?', $hash);
                
                $row = $this->fetchRow($selectStatememt);

                // If the user id and the validfation hash both match and we find a result return true else return false
                if ($row instanceof Zend_Db_Table_Row){
                    return true;
                }else{
                    return false;
                }
                
            }else{
                throw new Exception('Unable to validate password hash in  User::validateHash, both arguments must be of type int'.$userId);
            }
            
        } catch (Exception $e) {
            echo 'Unable to validate hash in User model: '.$e->getMessage();
        }
        
    }
    
    /*
     * This function updates the users details and removes the forgot password hash
     * 
     * @param array $formData
     * @param int $userId
     * 
     */
    public function updateForgotPassword($formData){
         try {
            if (is_array($formData)){

                // Get the user ID
                $parts = explode(':', $formData['hash']);
                $userId = $parts[0];
                
                $where = $this->getAdapter()->quoteInto('id = ?', $userId);
                
                
                
                // Unset values we are not going to use
                unset($formData['hash']);
                unset($formData['password_repeat']);
                
                $formData['forgot_password_hash'] = '';
                $formData['password'] = sha1($formData['password'].'34idnTgs98');
                $formData['active'] = 1;
                
                $this->update($formData, $where);
                
            }else{
                throw new Exception('Unable to update user password in User::updateForgotPassword, arguments must be 1:array');
            }
            
        } catch (Exception $e) {
            echo 'Unable to update user forgot password: '.$e->getMessage();
        }
    }
    
    
}