<?php

/*
 * This is the form for adding / editing content types in the system
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Forms
 */
class Application_Form_ContentTypeForm extends Zend_Form
{
    
    public function init()
    {
       
        $this->setAttrib('class', 'contentType');
        $this->setAttrib('id', 'contentTypeForm');
        
        // Title input field
        $name = new Zend_Form_Element_Text('name');
        $name->addFilter('StringTrim')  
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->setRequired(true)
                ->addErrorMessage('Name is required')
                ->setLabel('Name:');
        
        // Submit input field
        $submit = new Zend_Form_Element_Submit('Save');
        $submit->setValue('Save')
                ->setAttrib('data-role', 'button')
                ->setAttrib('data-theme', 'e')
                ->setAttrib('class', 'submit');
        
        $this->addElements(array($name, $submit));
        
      
    }
    
    
}

