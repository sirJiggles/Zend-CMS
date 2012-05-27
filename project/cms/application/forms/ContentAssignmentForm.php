<?php

/*
 * This is the form for assigning content to regions on pages
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Forms
 */
class Application_Form_ContentAssignmentForm extends Zend_Form
{
    
    protected $_content = '';
    protected $_currentActive = '';

    public function init()
    {
        // Nothing in here ..
    }
    
    public function setValues($content, $currentActive){
        // Set the content so we can loop over it 
        $this->_content = $content;
        // Set the current active for this content so we can make it active
        $this->_currentActive = $currentActive;
    }
    
    public function startForm(){
    
        $this->setAttrib('class', 'page');
        $this->setAttrib('id', 'conentAssignment');
        
        
        $assignment = new Zend_Form_Element_Radio('assignment');
        $assignment->setAttrib('data-native-menu', 'false')
                ->setAttrib('data-theme', 'a')
                ->setAttrib('class', 'hide')
                ->setDecorators(array('ViewHelper',
                                'Description',
                                'Errors',
                                array(array('Input' => 'HtmlTag'), array('tag' => 'dd')),
                                array(array('row' => 'HtmlTag'), 
                                      array('tag' => 'fieldset', 'data-role' => 'controlgroup'))));
                                       
        
        // Loop through all the content for this type
        foreach ($this->_content as $content){
            
            $assignment->addMultiOption($content->id, $content->ref);
            
        }
        
        // Set the currently active item
        if ($this->_currentActive != 0){
            $assignment->setValue($this->_currentActive);
        }
        
        // add to the form
        $this->addElement($assignment);
        
         // Submit input field
        $submit = new Zend_Form_Element_Submit('Save');
        $submit->setValue('Save')
                ->setAttrib('data-theme', 'e')
                ->setAttrib('class', 'submit');
        
        // add the save button
        $this->addElement($submit);
        
    }
}

