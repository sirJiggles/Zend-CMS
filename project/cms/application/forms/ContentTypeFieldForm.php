<?php

/*
 * This is the form for adding / editing the content type fields in the system
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Forms
 */
class Application_Form_ContentTypeFieldForm extends Zend_Form
{
    
    public function init()
    {
       
        $this->setAttrib('class', 'contentTypeField');
        $this->setAttrib('id', 'contentTypeFieldForm');
        
        // Title input field
        $name = new Zend_Form_Element_Text('name');
        $name->addFilter('StringTrim')  
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->setRequired(true)
                ->addErrorMessage('Name is required')
                ->setLabel('Name:');
        
        // Format input field
        $format = new Zend_Form_Element_Select('format');
        $format->setLabel('Type:')
                ->addMultiOption('text', 'Text')
                ->addMultiOption('wysiwyg', 'Full Editor')
                ->addMultiOption('image', 'Image')
                ->setAttrib('data-native-menu', 'false')
                ->setAttrib('data-theme', 'a');
        
        // Submit input field
        $submit = new Zend_Form_Element_Submit('Save');
        $submit->setValue('Save')
                ->setAttrib('data-role', 'button')
                ->setAttrib('data-theme', 'e')
                ->setAttrib('class', 'submit');
        
        
        // Add the element that lets the system know the content type here
        $contentType = new Zend_Form_Element_Hidden('content_type');
        $contentType->addFilter('StringTrim')  
                ->addFilter(new Zend_Filter_HtmlEntities());
        
        
        $this->addElements(array($name, $format, $submit, $contentType));
        
      
    }
    
    
}

