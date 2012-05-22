<?php

/*
 * This is the model for prefoming CRUD operations on the content fields for 
 * content types
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Models
 */
class Application_Model_DataTypeFields extends Zend_Db_Table{
    
    protected $_name = 'content-type-fields';
    
    /*
     * Simple function to get all datatype fields from the content-types table
     * 
     * @return Zend_Db_Table data type fields
     */
    public function getAllDataTypeFields(){
        return $this->fetchAll();
    }
    
    /*
     * This function gets all of the data type fields for the data type by id
     * parsed to the system, for example if we have data type of 1 (article)
     * here we get all the data type fields for that data type (this is for the 
     * main page where superadmins can edit the fields for the content tyes)
     * 
     * @param int $id
     * @return Zend_Db_Table $users
     */
    public function getDataFieldsForDataType($id){
        try {
            $selectStatememt = $this->select()
                                    ->where('content_type = ?', $id);
            $row = $this->fetchAll($selectStatememt);
           
            return $row;
            
        } catch (Exception $e) {
            echo 'Unable to getDataFieldsForDataType in DataTypeFields model: '.$e->getMessage();
        }
    }
    
    /*
     * Function to get content types from the system by the name param
     * 
     * @param string $name
     * @param int $contentType
     * @return Zend_Db_Table_Row contentType
     */
    public function getContentFieldByName($name, $contentType){
        
        try {
            $selectStatememt = $this->select()
                                    ->where('name = ?', $name)
                                    ->where('content_type = ?', $contentType);
            $row = $this->fetchRow($selectStatememt);
           
            return $row;
            
        } catch (Exception $e) {
            echo 'Unable to getContentFieldByName in DataTypeFileds model: '.$e->getMessage();
        }
        
    }
    
    /*
     * Function to get the content type field from the database by the id
     * 
     * @param int $id
     * @return Zend_Db_Table_Row content type
     */
    public function getContentTypeFieldById($id){
        
        try {
            $selectStatememt = $this->select()
                                    ->where('id = ?', $id);
            $row = $this->fetchRow($selectStatememt);
           
            return $row;
            
        } catch (Exception $e) {
            echo 'Unable to getContentFieldById in DataTypeFields model: '.$e->getMessage();
        }
        
    }
    
    /*
     * This is the function to remove content type fields from the system
     * 
     * @param int $id
     * @return boolean task status
     */
    public function removeContentTypeField($id){
        try {
            
            // Get this item first from the db
            $selectStatememt = $this->select()
                                    ->where('id = ?', $id);
            $currentField = $this->fetchRow($selectStatememt);
            
            
            
            // First we need to remove all content that uses this field
            $contentTable = new Application_Model_Content();
            $content = $contentTable->getContentByType($currentField->content_type);
            
            // Reconstruct all of the content that used this field
            //$sql = 'UPDATE `content` SET `content` = ? WHERE `id` = ?';
            foreach($content as $row){
                $contentArray = unserialize($row->content);
                $newContent = array();
                foreach($contentArray as $field => $val){
                    if ($field != $currentField->name){
                        $newContent[$field] = $val;
                    }
                }
                
                
                $where = $contentTable->getAdapter()->quoteInto('id = ?', $row->id);
                $contentTable->update(array('content' => serialize($newContent)), $where);
                
                
                //$fakeData = array();
                //$fakeData['content'] = serialize($newContent);
                //$fakeData['id'] = $row->id;
                //$fakeData['content_type'] = $row->content_type;
                //$fakeData['ref'] = $row->ref;
                
                //$contentTable->updateContent($fakeData, $row->id);
                //$db->prepare($sql);
                //$db->execute(array(serialize($newContent), $row->id));
            }
            
            $where = $this->getAdapter()->quoteInto('id = ?', $id);
            $result = $this->delete($where);
            if ($result){
                return 'true';
            }else{
                return 'Unable to find content type field to delete';
            }
        } catch (Exception $e) {
            echo 'Unable to removeContentTypeField in DataTypeFields model: '.$e->getMessage();
        }
    }
    
    
    
    /*
     * Function to update content type fields based on the id
     * 
     * @param array $formData
     * @param array $id
     * @return boolean $result
     * 
     */
    public function updateContentTypeField($formData, $id){
        
        try {
            
            if (is_array($formData) && is_numeric($id)){
                
                /* 
                 * First get all the content types and make sure the title they 
                 * are tring to update does not already exist
                 */
                $contentTypeFields = $this->fetchAll();
                $nameTaken = false;
               
                foreach ($contentTypeFields as $contentTypeField){
                    if ($contentTypeField['id'] != $id){
                        if ($contentTypeField['content_type'] == $formData['content_type']){
                            // Check if the new title is taken
                            if ($contentTypeField['name'] == $formData['name']){
                                $nameTaken = true;
                                break;
                            } 
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
                throw new Exception('Incorrect variable types passed to updateContentTypeField: expecting array and int');
            }
            
        }catch (Exception $e) {
            echo 'Unable to updateContentTypeField in DataTypeFields model: '.$e->getMessage();
        }
        
    }
    
    /*
     * Function for aadding content types to the system
     * 
     * @param array $postData
     * @return boolean task result
     * 
     */
    public function addContentTypeField($formData){
        try {
            
            if (is_array($formData)){
                
                $contentTypeFields = $this->fetchAll();
                $nameTaken = false;
                
                foreach ($contentTypeFields as $curentTypeField){
                    if ($curentTypeField['content_type'] == $formData['content_type']){
                        if ($curentTypeField['name'] == $formData['name']){
                            $nameTaken = true;
                            break;
                        }
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
                throw new Exception('Incorrect variable types passed to addContentTypeField: expecting array of form data');
            }
            
        }catch (Exception $e) {
            echo 'Unable to addContentTypeField in DataTypeField model: '.$e->getMessage();
        }
    }
    
   
    
    
}