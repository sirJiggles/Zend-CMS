<?php

/*
 * This is the model that handles all of the Pages functionality
 * 
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Models
 */
class Application_Model_Pages extends Zend_Db_Table{
    
    protected $_name = 'pages';
    
    /*
     * Simple function to get all pages from the pages table
     * 
     * @return Zend_Db_Table $pages
     */
    public function getAllPages(){
        return $this->fetchAll();
    }
    
    /*
     * Function to get page from the system by the name param
     * 
     * @param string $name
     * @return Zend_Db_Table_Row page
     */
    public function getPageByName($name){
        
        try {
            $selectStatememt = $this->select()
                                    ->where('name = ?', $name);
            $row = $this->fetchRow($selectStatememt);
           
            return $row;
            
        } catch (Exception $e) {
            echo 'Unable to getPageByName in Pages model: '.$e->getMessage();
        }
        
    }
    
    /*
     * Function to get the pages from the database by the id
     * 
     * @param int $id
     * @return Zend_Db_Table_Row page
     */
    public function getPageById($id){
        
        try {
            $selectStatememt = $this->select()
                                    ->where('id = ?', $id);
            $row = $this->fetchRow($selectStatememt);
           
            return $row;
            
        } catch (Exception $e) {
            echo 'Unable to getPageById in Pages model: '.$e->getMessage();
        }
        
    }
    
    /*
     * This is the function to remove pages from the system
     * 
     * @param int $id
     * @return boolean task status
     */
    public function removePage($id){
        try {

            //remove the template form the system
            $where = $this->getAdapter()->quoteInto('id = ?', $id);
            $result = $this->delete($where);
            
            // return the right result based on weather we could remove it
            if ($result){
                return true;
            }else{
                return 'Unable to find page to delete';
            }
        } catch (Exception $e) {
            echo 'Unable to removePage in Pages model: '.$e->getMessage();
        }
    }

    /*
     * Function to update pages based on the id
     * 
     * @param array $formData
     * @param array $id
     * @return boolean $result
     * 
     */
    public function updatePage($formData, $id){
        
        try {
            
            if (is_array($formData) && is_numeric($id)){
                
                /* 
                 * First get all the pages and make sure the name they 
                 * are tring to update does not already exist
                 */
                $pages = $this->fetchAll();
                $nameTaken = false;
               
                foreach ($pages as $page){
                    if ($page['id'] != $id){
                        // Check if the new title is taken
                        if ($page['name'] == $formData['name']){
                            $nameTaken = true;
                            break;
                        } 
                       
                    }
                }
                
                if (!$nameTaken){
                    
                    $where = $this->getAdapter()->quoteInto('id = ?', $id);
                    $newContentTypes = array();
                    // Sort the content type form field
                    foreach($formData as $field => $value){
                        $parts = explode('_', $field);
                        if ($parts[0] == 'content'){
                            
                            // append the id of contnt type as key and val as val
                            $newContentTypes[$parts[1]] = $value;
                            // remove this form field from the array
                            unset($formData[$field]);
                        }
                    }
                    // spoof the content_types value
                    $formData['content_types'] = serialize($newContentTypes);
                    
                    
                    $this->update($formData, $where);
                    
                    return true;
                }else{
                    
                    return 'Name Taken';
                }
            }else{
                throw new Exception('Incorrect variable types passed to updatePage: expecting array and int');
            }
            
        }catch (Exception $e) {
            echo 'Unable to updatePage in Pages model: '.$e->getMessage();
        }
        
    }
    
    /*
     * Function for aadding pages to the system
     * 
     * @param array $postData
     * @return boolean task result
     * 
     */
    public function addPage($formData){
        try {
            
            if (is_array($formData)){
                
                $pages = $this->fetchAll();
                $nameTaken = false;
                
                foreach ($pages as $page){
                    if ($page['name'] == $formData['name']){
                        $nameTaken = true;
                        break;
                    }
                   
                }
                
                if (!$nameTaken){
                    
                    // Add database record
                    
                    // Get the template the user has selected from the API
                    $templateModel = new Application_Model_Templates();
                    $template = $templateModel->getTemplateById($formData['template']);
                    
                    $contentTypes = unserialize($template->content_types);
                    
                    $newContentAssigned = array();
                    
                    foreach($contentTypes as $contentType){
                        for ($i = 0; $i < $contentType['amount']; $i ++){
                            $newContentAssigned[] = array('type' => $contentType['type'],
                                                          'value' => 0);
                        }

                    }
                    // spoof the content_assigned value
                    $formData['content_assigned'] = serialize($newContentAssigned);
                    
                    $newRow = $this->createRow($formData);
                    $newRow->save();
                    
                    return true;
                }else{
                    return 'Name Taken';
                }
                
                
                
            }else{
                throw new Exception('Incorrect variable types passed to addPage: expecting array of form data');
            }
            
        }catch (Exception $e) {
            echo 'Unable to addPage in Pages model: '.$e->getMessage();
        }
    }
    
   
    
    
}