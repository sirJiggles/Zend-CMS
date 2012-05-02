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
    
    /*
     * This function gets all the API users bar the default admin one
     * 
     * @return array Zend_Db_Table_Row
     */
    public function getApiUsers(){
        
         try {
            $selectStatememt = $this->select()
                                    ->where('id != ?', '1');
            $row = $this->fetchAll($selectStatememt);
           
            return $row;
            
        } catch (Exception $e) {
            echo 'Unable to getApiUsers in API model: '.$e->getMessage();
        }
    }
    
    /*
     * This function deals with getting an API user by user ID
     * 
     * @param int $userId
     * @return object Zend_Db_Table_Row
     */
    public function getUserById($userId){
        try {
            $selectStatememt = $this->select()
                                    ->where('id = ?', $userId);
            $row = $this->fetchRow($selectStatememt);
           
            return $row;
            
        } catch (Exception $e) {
            echo 'Unable to getUserById in API model: '.$e->getMessage();
        }
    }
    
    /*
     * This is the function to get api users by the ref
     * 
     * @param string $ref
     * @return object Zend_Db_Table_Row 
     */
    public function getUserByRef($ref){
        try {
            $selectStatememt = $this->select()
                                    ->where('ref = ?', $ref);
            $row = $this->fetchRow($selectStatememt);
            
            if ($row->id != 1){
                return $row;
            }else{
                return false;
            }
            
        } catch (Exception $e) {
            echo 'Unable to getUserByRef in API model: '.$e->getMessage();
        }
    }
    
    /*
     * This is the function to get api users by the key
     * 
     * @param string $key
     * @return object Zend_Db_Table_Row 
     */
    public function getUserByKey($key){
        try {
            $selectStatememt = $this->select()
                                    ->where('key = ?', $key);
            $row = $this->fetchRow($selectStatememt);
            
            if ($row->id != 1){
                return $row;
            }else{
                return false;
            }
            
        } catch (Exception $e) {
            echo 'Unable to getUserByKey in API model: '.$e->getMessage();
        }
    }
    
    /*
     * This is the function to remove api users from the system
     * 
     * @param int $userId
     * @return boolean task status
     */
    public function removeUser($userId){
        try {
            if ($userId == 1){
                return false;
            }
            $where = $this->getAdapter()->quoteInto('id = ?', $userId);
            $result = $this->delete($where);
            if ($result){
                return true;
            }else{
                return 'Unable to find api user to delete';
            }
        } catch (Exception $e) {
            echo 'Unable to removeUser in API model: '.$e->getMessage();
        }
    }
    
     /*
     * Function for adding API users to the system
     * 
     * @param array $postData
     * @return boolean task result
     * 
     */
    public function addUser($formData){
        try {
            
            if (is_array($formData)){
                
                $currentUsers = $this->fetchAll();
                $refTaken = false;
                $keyTaken = false;
                
                foreach ($currentUsers as $currentUser){
                    if ($currentUser['ref'] == $formData['ref']){
                        $refTaken = true;
                        break;
                    }
                    if ($currentUser['key'] == $formData['key']){
                        $keyTaken = true;
                        break;
                    }
                    
                }
                
                if (!$refTaken && !$keyTaken){

                    // Add database record
                    $newRow = $this->createRow($formData);
                    $newRow->save();
                    
                    return true;
                }else{
                    if ($refTaken){
                        return 'Ref Taken';
                    }
                    if ($keyTaken){
                        return 'Key Taken';
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
     * Function to update API users based on the post data
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
                $refTaken = false;
                $keyTaken = false;
                
                foreach ($currentUsers as $currentUser){
                    if ($currentUser['id'] != $userId){
                        // Check if the new username is taken
                        if ($currentUser['ref'] == $formData['ref']){
                            $refTaken = true;
                            break;
                        } 
                        if ($currentUser['key'] == $formData['key']){
                            $keyTaken = true;
                            break;
                        }
                    }
                }
                
                if (!$refTaken && !$keyTaken){

                    $where = $this->getAdapter()->quoteInto('id = ?', $userId);

                    $this->update($formData, $where);
                    
                    return true;
                }else{
                    if ($refTaken){
                        return 'Ref Taken';
                    }
                    if ($keyTaken){
                        return 'Key Taken';
                    }
                }
            }else{
                throw new Exception('Incorrect variable types passed to updateAPIUser: expecting array and int');
            }
            
        }catch (Exception $e) {
            echo 'Unable to updateUser in API model: '.$e->getMessage();
        }
        
    }
    
}