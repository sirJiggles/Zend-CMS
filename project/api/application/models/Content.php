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
     * This function connects to the data type fields table to get 
     * the fields for the current content type so we can valuidate
     * the type of data that is comming in, I know this is a little
     * hacky but it works for now
     * 
     * @param int $id
     * @return array $results
     */
    public function getFieldsForContentType($id){
        
        $sql = 'SELECT * FROM `content-type-fields` WHERE `content_type` = ?';
        $db = $this->getDefaultAdapter();
        $rows = $db->fetchAll($sql, array($id));
        return $rows;
        
    }
    
    
    
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
     * This is the function to get content by ref
     * 
     * @param string ref
     * @return object Zend_Db_Table_Row
     */
    public function getByRef($ref){
        try {
            $selectStatememt = $this->select()
                                    ->where('ref = ?', $ref);
            $row = $this->fetchRow($selectStatememt);
            
            return $row;
          
        } catch (Exception $e) {
            echo 'Unable to getByRef in Content model: '.$e->getMessage();
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
                
                // variable defs
                $contentToStore = array();
                $fields = array();
                
                // Sanity checking on required inputs
                if (!isset($formData['ref']) || !isset($formData['content_type'])){
                    return false;
                }
                
                // First make sure the ref is not taken
                $currentContentItems = $this->getByRef($formData['ref']);
                
                if ($currentContentItems !== null){
                    return 'Ref Taken';
                }
                
                // sort the data
                foreach($formData as $key => $value){
                    if($key != 'content_type' && $key != 'ref'){
                        // proceed
                        $contentToStore[$key][] = $value;
                        $fields[] = $key;
                    }
                }
                
                // Now check that the content type exists and expects the form
                // fields we are about to send it or things could get ugly
                $contentTypeFields = $this->getFieldsForContentType($formData['content_type']);
                
                if ($contentTypeFields === null){
                    return false;
                }
                
                
                foreach($contentTypeFields as $currentField){
                   if (!key_exists($currentField['name'], $formData)){
                        return false;
                   }
                }

                // format the content to go in the db
                $serealizedContent = serialize($contentToStore);
                
                // Save all data as new row to go in db
                $createRow = array('content_type' => $formData['content_type'],
                                    'content' => $serealizedContent,
                                    'ref' => $formData['ref']);
                
                // Actualy run the add command
                $newRow = $this->createRow($createRow);
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
                
                // variable defs
                $contentToStore = array();
                $fields = array();
                
                // Sanity checking on required inputs
                if (!isset($formData['ref']) || !isset($formData['content_type'])){
                    return false;
                }
                
                // First make sure the ref is not taken
                $currentContentItems = $this->getByRef($formData['ref']);
                
                if ($currentContentItems !== null){
                    if ($currentContentItems->id != $id){
                        return 'Ref Taken';
                    }
                }
                
                // sort the data
                foreach($formData as $key => $value){
                    if($key != 'content_type' && $key != 'ref'){
                        // proceed
                        $contentToStore[$key][] = $value;
                        $fields[] = $key;
                    }
                }
                
                // Now check that the content type exists and expects the form
                // fields we are about to send it or things could get ugly
                $contentTypeFields = $this->getFieldsForContentType($formData['content_type']);
                
                if ($contentTypeFields === null){
                    return false;
                }
                
                foreach($contentTypeFields as $currentField){
                   if (!key_exists($currentField['name'], $formData)){
                        return false;
                   }
                }

                // format the content to go in the db
                $serealizedContent = serialize($contentToStore);
                
                // Save all data as new row to go in db
                $updateRow = array('content_type' => $formData['content_type'],
                                    'content' => $serealizedContent,
                                    'ref' => $formData['ref']);
                
                 // Update the data
                $where = $this->getAdapter()->quoteInto('id = ?', $id);
                $this->update($updateRow, $where);
                
                
                return true; 
                
            }else{
                throw new Exception('Incorrect variable types passed to updateContent: expecting array of form data and content id');
            }
            
        }catch (Exception $e) {
            echo 'Unable to updateContent in Content model: '.$e->getMessage();
        }
    }
    
}