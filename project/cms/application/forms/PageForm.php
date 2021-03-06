<?php

/*
 * This is the form for pages in the system
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Forms
 */
class Application_Form_PageForm extends Zend_Form
{
    
    protected $_templates = '';

    public function init()
    {
        // Nothing in here ..
    }
    
    public function setValues($templates){
        // Set the templates for the form
        $this->_templates = $templates;
    }
    
    public function startForm(){
    
        if ($this->_templates){
            
            $this->setAttrib('class', 'page');
            $this->setAttrib('id', 'pageForm');

            // Name input field
            $name = new Zend_Form_Element_Text('name');
            $name->setRequired(true)
                    ->addFilter(new Zend_Filter_HtmlEntities())
                    ->addErrorMessage('Name is required')
                    ->setLabel('Page name:');

            $this->addElement($name);
            
            
            $currentAction = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
            
            // Only show template assignment on add page not edit else content
            // assignments get mesed up
            if ($currentAction == 'add'){
            
                $templateElement = new Zend_Form_Element_Select('template');
                $templateElement->setLabel('Template:')
                        ->setAttrib('data-native-menu', 'false')
                        ->setAttrib('data-theme', 'a');

                // Loop through all of the files in the templates directory and add
                // them to the select box for template file
                foreach ($this->_templates as $template){

                    $templateElement->addMultiOption($template->id, $template->name);

                }
                // add to the form
                $this->addElement($templateElement);
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
}

