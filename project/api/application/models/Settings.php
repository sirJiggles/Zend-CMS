<?php

/*
 * This is the model that handles all of the Site Setting specific fucntionality
 * 
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Models
 */
class Application_Model_Settings extends Zend_Db_Table{
    
    protected $_name = 'settings';
    
    /*
     * This is the function that deals with updating the site settings
     * 
     * @param array $formData
     * @return boolean
     */
    public function updateSettings($formData){
       
        try {
            
            if (is_array($formData)){
                    
                $this->update($formData, 'id = 1');

                return true;
            }else{
                throw new Exception('Incorrect variable type passed to updateSettings: expecting array');
            }
            
        }catch (Exception $e) {
            echo 'Unable to updateSettings in Settings model: '.$e->getMessage();
        }
        
    }
    
    /*
     * Get the site settings from the database
     */
    public function getSettings(){
        try {
            
           $selectStatememt = $this->select()
                                    ->where('id = 1');
                
            $row = $this->fetchRow($selectStatememt);
            
            return $row;
            
        }catch (Exception $e) {
            echo 'Unable to get settings in Settings model: '.$e->getMessage();
        }
    }
    
}