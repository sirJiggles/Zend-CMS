<?php

/*
 * This model handels all of the page structure specific table operations
 * 
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Models
 */
class Application_Model_Structure extends Zend_Db_Table{
    
    protected $_name = 'structure';
    
    // Get the page structure from the table
    public function getStructure(){
        try {
            
            $select = $this->select()->where('id = 1');
            $row = $this->fetchRow($select);
            return $row;
            
        }catch (Exception $e) {
            echo 'Unable to get structure data in Structure model: '.$e->getMessage();
        }
    }
    
   
    public function updateStructure($structure){
        
        try {
            
            if (is_string($structure)){
                
                $where = $this->getAdapter()->quoteInto('id = ?', '1');
                $data = array('structure' => serialize($structure));
                $this->update($data ,$where);

                return true;
            }else{
                throw new Exception('Incorrect variable types passed to updateStructure: expecting array');
            }
            
        }catch (Exception $e) {
            echo 'Unable to updateStructure in Structure model: '.$e->getMessage();
        }
        
    }
    
}