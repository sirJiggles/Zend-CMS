<?php

/*
 * This is the form for adding content into the system it is parsed the contend
 * fields oject from the api so we can decide what inputs to add on this form
 * instance
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Forms
 */
class Application_Form_ContentForm extends Zend_Form
{
    
    protected $_contentFields = '';
    
    public function init()
    {
        // Nothing in here anymore..
    }
    
    public function setValues($contentFields){
        // Set the value of the content fields for this form
        $this->_contentFields = $contentFields;
    }
    
    public function startForm(){
        
        $this->setAttrib('class', 'content');
        $this->setAttrib('id', 'contentForm');
        
        /*
         * Add the ref field to th form, this is the reference that the user
         * has to create for each bit of content it has to be uneque within its
         * content type (so only 1 article called 'article one' and so on)
         */
        $ref = new Zend_Form_Element_Text('ref');
        $ref->addFilter('StringTrim')  
            ->addFilter(new Zend_Filter_HtmlEntities())
            ->setRequired(true)
            ->addErrorMessage('Reference is required')
            ->setLabel('Reference:');   
        $this->addElement($ref);
        
        
        
        // Loop through all of the content fields and add them to the system
        // based on the field type
        foreach($this->_contentFields as $field){
            switch($field->format){
                case 'text':
                    // Reset item first
                    $item = '';
                    
                    // Add new text element to the form
                    $item = new Zend_Form_Element_Text($field->name);
                    $item->addFilter('StringTrim')  
                        ->addFilter(new Zend_Filter_HtmlEntities())
                        ->setRequired(true)
                        ->addErrorMessage(ucfirst($field->name).' is required')
                        ->setLabel(ucfirst($field->name).':');   
                    $this->addElement($item);
                    break;
                case 'image':
                    // For now we will just make this an text input
                    $item = '';
                    
                    // Add new text element to the form
                    $item = new Zend_Form_Element_Text($field->name);
                    $item->addFilter('StringTrim')  
                        ->addFilter(new Zend_Filter_HtmlEntities())
                        ->setRequired(true)
                        ->addErrorMessage(ucfirst($field->name).' is required')
                        ->setLabel(ucfirst($field->name).':');   
                    $this->addElement($item);
                    break;
                case 'wysiwyg':
                     // Reset item first
                    $item = '';
                    
                    // Add new textarea element to the form
                    $item = new Zend_Form_Element_Textarea($field->name);
                    $item->addFilter('StringTrim')  
                        ->addFilter(new Zend_Filter_HtmlEntities())
                        ->setRequired(true)
                        ->addErrorMessage(ucfirst($field->name).' is required')
                        ->setLabel(ucfirst($field->name).':');   
                    $this->addElement($item);
                    break;
                default:
                    // unrecognised format so ignore
                    break;
            }
            
            
        }
        
        // Submit input field
        $submit = new Zend_Form_Element_Submit('Save');
        $submit->setValue('Save')
            ->setAttrib('data-role', 'button')
            ->setAttrib('data-theme', 'e')
            ->setAttrib('class', 'submit');

        $this->addElement($submit);
           
    }
    
    
}

