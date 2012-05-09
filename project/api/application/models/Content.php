<?php

/*
 * This is the model that handles all of the content functionality
 * 
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Models
 */
class Application_Model_Content extends Zend_Db_Table{
    
    protected $_name = 'content';
    
    
    /*
     * This function is for getting content by an id
     * 
     * @param int $id
     * @return object Zend_Db_Table_Row
     */
    public function getContentById($id){
        try {
            $selectStatememt = $this->select()
                                    ->where('id = ?', $id);
            $row = $this->fetchRow($selectStatememt);
           
            return $row;
            
        } catch (Exception $e) {
            echo 'Unable to getContentById in Content model: '.$e->getMessage();
        }
    }
    
    /*
     * This is the function to get content by content type from the api
     * 
     * @param string $type
     * @return object Zend_Db_Table_Row 
     */
    public function getContentByType($type){
        try {
            $selectStatememt = $this->select()
                                    ->where('content_type = ?', $type);
            $row = $this->fetchAll($selectStatememt);
            
            return $row;
          
        } catch (Exception $e) {
            echo 'Unable to getContentByType in Content model: '.$e->getMessage();
        }
    }
    
    
    /*
     * This is the function to remove content from the system
     * 
     * @param int $id
     * @return boolean task status
     */
    public function removeContent($id){
        try {
            $where = $this->getAdapter()->quoteInto('id = ?', $id);
            $result = $this->delete($where);
            if ($result){
                return true;
            }else{
                return 'Unable to find system content to delete';
            }
        } catch (Exception $e) {
            echo 'Unable to removeContent in Content model: '.$e->getMessage();
        }
    }
    
     /*
     * Function for adding API users to the system
     * 
     * @param array $postData
     * @return boolean task result
     * 
     */
    public function addContent($formData){
        try {
            
            if (is_array($formData)){
                
                // Flag for identifing if we have the content type id
                $typeFound = false;
                // Var for string the content type id
                $contentType = '';
                // Array for storing all of the data
                $contentToStore = array();
                
                // Here we grab all of the form inputs and serialise the data in on array
                foreach ($formData as $key => $value){
                    if ($key == 'content_type'){
                        $typeFound = true;
                        $contentType = $value;
                    }else{
                       $contentToStore[$key][] = $value; 
                    }
                }
                
                if (!$typeFound){
                    return false;
                }
                
                $newData = serialize($contentToStore);
                
                $newRow = $this->createRow(array('content_type' => $contentType, 'content' => $newData));
                $newRow->save();
               
               return true; 
                
            }else{
                throw new Exception('Incorrect variable types passed to addContent: expecting array of form data');
            }
            
        }catch (Exception $e) {
            echo 'Unable to addContent in Content model: '.$e->getMessage();
        }
    }
    
    /*
     * This is the function for updateting content via the API
     * 
     * @param array formData
     * @param int $id
     * @return boolean taskresult
     */
    
    public function updateContent($formData, $id){
         try {
            
            if (is_array($formData)){
                
                // Flag for identifing if we have the content type id
                $typeFound = false;
                // Var for string the content type id
                $contentType = '';
                // Array for storing all of the data
                $contentToStore = array();
                
                // Here we grab all of the form inputs and serialise the data in on array
                foreach ($formData as $key => $value){
                    if ($key == 'content_type'){
                        $typeFound = true;
                        $contentType = $value;
                    }else{
                       $contentToStore[$key][] = $value; 
                    }
                }
                
                if (!$typeFound){
                    return false;
                }
                
                $newData = serialize($contentToStore);
                
                // Update the data
                $updateData = array('content_type' => $contentType, 'content' => $newData);
                $where = $this->getAdapter()->quoteInto('id = ?', $id);
                $this->update($updateData, $where);
                
                
                return true; 
                
            }else{
                throw new Exception('Incorrect variable types passed to updateContent: expecting array of form data and content id');
            }
            
        }catch (Exception $e) {
            echo 'Unable to updateContent in Content model: '.$e->getMessage();
        }
    }
    
}