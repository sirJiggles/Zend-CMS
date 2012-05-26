<?php

/*
 * This is the model that handles all of the Template functionality
 * 
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Models
 */
class Application_Model_Templates extends Zend_Db_Table{
    
    protected $_name = 'templates';
    
    /*
     * Simple function to get all templates from the templates table
     * 
     * @return Zend_Db_Table $templates
     */
    public function getAllTemplates(){
        return $this->fetchAll();
    }
    
    /*
     * Function to get templates from the system by the name param
     * 
     * @param string $name
     * @return Zend_Db_Table_Row contentType
     */
    public function getTemplateByName($name){
        
        try {
            $selectStatememt = $this->select()
                                    ->where('name = ?', $name);
            $row = $this->fetchRow($selectStatememt);
           
            return $row;
            
        } catch (Exception $e) {
            echo 'Unable to getTemplateByName in Templates model: '.$e->getMessage();
        }
        
    }
    
    /*
     * Function to get the templates from the database by the id
     * 
     * @param int $id
     * @return Zend_Db_Table_Row template
     */
    public function getTemplateById($id){
        
        try {
            $selectStatememt = $this->select()
                                    ->where('id = ?', $id);
            $row = $this->fetchRow($selectStatememt);
           
            return $row;
            
        } catch (Exception $e) {
            echo 'Unable to getTemplateById in Templates model: '.$e->getMessage();
        }
        
    }
    
    /*
     * This is the function to remove templates from the system
     * 
     * @param int $id
     * @return boolean task status
     */
    public function removeTemplate($id){
        try {

            // @TODO remove all of the 'content assigned' to this template number
            
            //remove the template form the system
            $where = $this->getAdapter()->quoteInto('id = ?', $id);
            $result = $this->delete($where);
            
            // return the right result based on weather we could remove it
            if ($result){
                return true;
            }else{
                return 'Unable to find template to delete';
            }
        } catch (Exception $e) {
            echo 'Unable to removeTemplate in Template model: '.$e->getMessage();
        }
    }
    
    
    
    /*
     * Function to update templates based on the id
     * 
     * @param array $formData
     * @param array $id
     * @return boolean $result
     * 
     */
    public function updateTemplate($formData, $id){
        
        try {
            
            if (is_array($formData) && is_numeric($id)){
                
                /* 
                 * First get all the templates and make sure the name they 
                 * are tring to update does not already exist
                 */
                $templates = $this->fetchAll();
                $nameTaken = false;
               
                foreach ($templates as $template){
                    if ($template['id'] != $id){
                        // Check if the new title is taken
                        if ($template['name'] == $formData['name']){
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
                            
                            if ($value != 0){
                                // append the id of contnt type as key and val as val
                                $newContentTypes[] = array( 'type' => $parts[1],
                                                            'amount' => $value);
                            }
                            // remove this form field from the array
                            unset($formData[$field]);
                        }
                    }
                    // spoof the content_types value
                    $formData['content_types'] = serialize($newContentTypes);
                    
                    
                    $this->update($formData, $where);
                    
                    /*
                     * The superadmins could have increased the amount of conternt types on 
                     * existing pages so we need to get all pages and add / remove the
                     * content assignment based on the template update
                     */
                    
                    $pagesModel = new Application_Model_Pages();
                    $allPages = $pagesModel->getAllPages();
                    foreach($allPages as $page){
                        // check if it uses this template
                        if($page->template == $id){
                            // Update the content assignment (this may leave some content unassigned)
                            $contentAssignment = unserialize($page->content_assigned);
                           
                            // count the amount of each type in the new template
                            $typeCountNew = array();
                            foreach($newContentTypes as $templateAssignment){
                                $typeCountNew[$templateAssignment['type']] = $templateAssignment['amount']; 
                            }
                            
                            // rebuild the content assignment based on the amount
                            // of new types for each field
                            $newData = array();
                            $typesUsed = array();
                            foreach($contentAssignment as $map){
                                // if the amount of types we have used is within the amount 
                                // for that type in the template add it to the new data
                                if($typesUsed[$map['type']] < $typeCountNew[$map['type']]){
                                    $newData[] = array('type' => $map['type'],
                                                       'value' => $map['value']);
                                    if (!isset( $typesUsed[$map['type']])){
                                         $typesUsed[$map['type']] = 1;
                                    }else{
                                        $typesUsed[$map['type']] = $typesUsed[$map['type']] + 1;
                                    }
                                    
                                }
                            }
                            
                            // now add any new types if we have not reached the quota for the 
                            // amount of new types
                            foreach($typesUsed as $type => $amountUsed){
                                if ($amountUsed < $typeCountNew[$type]){
                                    while($typesUsed[$type] <= $typeCountNew[$type]){
                                        $newData[] = array('type' => $type,
                                                            'value' => 0);
                                        $typesUsed[$type] ++;
                                         if (!isset( $typesUsed[$type])){
                                            $typesUsed[$type] = 1;
                                        }else{
                                            $typesUsed[$type] = $typesUsed[$type] + 1;
                                        }
                                    }
                                }
                            }
                            
                            $fakeData = array();
                            $fakeData['name'] = $page->name;
                            $fakeData['template'] = $page->template;
                            $fakeData['content_assigned'] = serialize($newData);
                            
                            // Finally save this new map to the page
                            $pagesModel->updatePage($fakeData, $page->id);
                        }
                    }
                    
                    
                    return true;
                }else{
                    
                    return 'Name Taken';
                }
            }else{
                throw new Exception('Incorrect variable types passed to updateTemplate: expecting array and int');
            }
            
        }catch (Exception $e) {
            echo 'Unable to updateTemplate in Templates model: '.$e->getMessage();
        }
        
    }
    
    /*
     * Function for aadding templates to the system
     * 
     * @param array $postData
     * @return boolean task result
     * 
     */
    public function addTemplate($formData){
        try {
            
            if (is_array($formData)){
                
                $templates = $this->fetchAll();
                $nameTaken = false;
                
                foreach ($templates as $template){
                    if ($template['name'] == $formData['name']){
                        $nameTaken = true;
                        break;
                    }
                   
                }
                
                if (!$nameTaken){

                    // Add database record
                    
                    $newContentTypes = array();
                    // Sort the content type form field
                    foreach($formData as $field => $value){
                        $parts = explode('_', $field);
                        if ($parts[0] == 'content'){
                            
                            if ($value != 0){
                                // append the id of contnt type as key and val as val
                                $newContentTypes[] = array( 'type' => $parts[1],
                                                            'amount' => $value);
                            }
                            // remove this form field from the array
                            unset($formData[$field]);
                        }
                    }
                    
                    // spoof the content_types value
                    $formData['content_types'] = serialize($newContentTypes);
                    
                    $newRow = $this->createRow($formData);
                    $newRow->save();
                    
                    return true;
                }else{
                    return 'Name Taken';
                }
                
                
                
            }else{
                throw new Exception('Incorrect variable types passed to addTemplate: expecting array of form data');
            }
            
        }catch (Exception $e) {
            echo 'Unable to addTemplate in Templates model: '.$e->getMessage();
        }
    }
    
   
    
    
}