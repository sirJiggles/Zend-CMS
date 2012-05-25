<?php

/*
 * This is the template form for adding / editing templates in the system
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Forms
 */
class Application_Form_TemplateForm extends Zend_Form
{
    
    protected $_templateFiles = '';
    protected $_contentTypes = '';
    
    public function init()
    {
        // Nothing in here ..
    }
    
    public function setValues($files, $contentTypes){
        // Set the template files for the form select box
        $this->_templateFiles = $files;
        $this->_contentTypes = $contentTypes;
    }
    
    public function startForm(){
    
        $this->setAttrib('class', 'template');
        $this->setAttrib('id', 'templateForm');

        // Name input field
        $name = new Zend_Form_Element_Text('name');
        $name->setRequired(true)
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->addErrorMessage('Name is required')
                ->setLabel('Template name:');
        
        $this->addElement($name);
       
        
        $fileElement = new Zend_Form_Element_Select('file');
        $fileElement->setLabel('File:')
                ->setAttrib('data-native-menu', 'false')
                ->setAttrib('data-theme', 'a');
        
        // Loop through all of the files in the templates directory and add
        // them to the select box for template file
        foreach ($this->_templateFiles as $file){
            
            $fileElement->addMultiOption(strtolower($file), $file);
            
        }
        // add to the form
        $this->addElement($fileElement);
        
        // Loop through all the content types and add a text area for each
      
        foreach($this->_contentTypes as $contentType){

            $element = new Zend_Form_Element_Text('content-'.$contentType->id);
            $element->addFilter(new Zend_Filter_HtmlEntities())
                ->addValidator(new Zend_Validate_Int())
                ->addErrorMessage('Amount of type must be numeric')
                ->setValue('0')
                ->setLabel(ucfirst($contentType->name).':');
            $this->addElement($element);
            
        }
        
        // Submit input field
        $submit = new Zend_Form_Element_Submit('Save');
        $submit->setValue('Save')
                ->setAttrib('data-theme', 'e')
                ->setAttrib('class', 'submit');
        
        // add the save button
        $this->addElement($submit);

        
    }
}

