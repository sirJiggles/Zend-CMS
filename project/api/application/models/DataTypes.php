<?php

/*
 * This is the model that handles all of the DataTypes functionality
 * 
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Models
 */
class Application_Model_DataTypes extends Zend_Db_Table{
    
    protected $_name = 'content-types';
    
    /*
     * Simple function to get all datatypes from the content-types table
     * 
     * @return Zend_Db_Table $users
     */
    public function getAllDataTypes(){
        return $this->fetchAll();
    }
    
    /*
     * Function to get content types from the system by the name param
     * 
     * @param string $name
     * @return Zend_Db_Table_Row contentType
     */
    public function getContentTypeByName($name){
        
        try {
            $selectStatememt = $this->select()
                                    ->where('name = ?', $name);
            $row = $this->fetchRow($selectStatememt);
           
            return $row;
            
        } catch (Exception $e) {
            echo 'Unable to getContentTypeByName in DataTypes model: '.$e->getMessage();
        }
        
    }
    
    /*
     * Function to get the content types from the database by the id
     * 
     * @param int $id
     * @return Zend_Db_Table_Row content type
     */
    public function getContentTypeById($id){
        
        try {
            $selectStatememt = $this->select()
                                    ->where('id = ?', $id);
            $row = $this->fetchRow($selectStatememt);
           
            return $row;
            
        } catch (Exception $e) {
            echo 'Unable to getContentTypeById in DataTypes model: '.$e->getMessage();
        }
        
    }
    
    /*
     * This is the function to remove content types from the system
     * 
     * @param int $id
     * @return boolean task status
     */
    public function removeContentType($id){
        try {
            $where = $this->getAdapter()->quoteInto('id = ?', $id);
            $result = $this->delete($where);
            if ($result){
                return true;
            }else{
                return 'Unable to find content type to delete';
            }
        } catch (Exception $e) {
            echo 'Unable to removeContentType in DataTypes model: '.$e->getMessage();
        }
    }
    
    
    
    /*
     * Function to update content types based on the id
     * 
     * @param array $formData
     * @param array $id
     * @return boolean $result
     * 
     */
    public function updateContentType($formData, $id){
        
        try {
            
            if (is_array($formData) && is_numeric($id)){
                
                /* 
                 * First get all the content types and make sure the title they 
                 * are tring to update does not already exist
                 */
                $contentTypes = $this->fetchAll();
                $nameTaken = false;
               
                foreach ($contentTypes as $contentType){
                    if ($contentType['id'] != $id){
                        // Check if the new title is taken
                        if ($contentType['name'] == $formData['name']){
                            $nameTaken = true;
                            break;
                        } 
                       
                    }
                }
                
                if (!$nameTaken){
                    
                    $where = $this->getAdapter()->quoteInto('id = ?', $id);

                    $this->update($formData, $where);
                    
                    return true;
                }else{
                    
                    return 'Name Taken';
                }
            }else{
                throw new Exception('Incorrect variable types passed to updateContentType: expecting array and int');
            }
            
        }catch (Exception $e) {
            echo 'Unable to updateContentType in DataTypes model: '.$e->getMessage();
        }
        
    }
    
    /*
     * Function for aadding content types to the system
     * 
     * @param array $postData
     * @return boolean task result
     * 
     */
    public function addContentType($formData){
        try {
            
            if (is_array($formData)){
                
                $currentTypes = $this->fetchAll();
                $nameTaken = false;
                
                foreach ($currentTypes as $curentType){
                    if ($curentType['name'] == $formData['name']){
                        $nameTaken = true;
                        break;
                    }
                   
                }
                
                if (!$nameTaken){

                    // Add database record
                    $newRow = $this->createRow($formData);
                    $newRow->save();
                    
                    return true;
                }else{
                    return 'Name Taken';
                }
                
                
                
            }else{
                throw new Exception('Incorrect variable types passed to addContentType: expecting array of form data');
            }
            
        }catch (Exception $e) {
            echo 'Unable to addContentType in DataTypes model: '.$e->getMessage();
        }
    }
    
   
    
    
}