<?php

/*
 * This is the user form, its used for both the edit user and add user actions
 * 
 * Gareth Fuller
 */

class Application_Form_UserForm extends Zend_Form
{
    
    public function init()
    {
        // Username input field
        $username = new Zend_Form_Element_Text('username');
        $username->addFilter('StringTrim')
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->setRequired(true)
                ->addErrorMessage('Username is required')
                ->setLabel('Username');
        
        // Password input field
        $password = new Zend_Form_Element_Password('password');
        $password->addErrorMessage('Password is required')
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->setLabel('Password');
        
        // Password repeat field
        $passwdRepeat = new Zend_Form_Element_Password('password_repeat');
        $passwdRepeat->addErrorMessage('Passwords don\'t match')
                ->addValidator('Identical', false, array('token' => 'password'))
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->setLabel('Reapeat Password');
        
        
        // First Name input field
        $firstName = new Zend_Form_Element_Text('first_name');
        $firstName->addFilter('StringTrim')
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->setRequired(true)
                ->addErrorMessage('First name is required')
                ->setLabel('First Name');
        
        // Lastname input field
        $lastName = new Zend_Form_Element_Text('last_name');
        $lastName->addFilter('StringTrim')
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->setRequired(true)
                ->addErrorMessage('Last name is required')
                ->setLabel('Last Name');
        
        // Role input field
        $role = new Zend_Form_Element_Select('role');
        $role->setLabel('Role')
                ->addMultiOption('admin', 'Admin')
                ->addMultiOption('editor', 'Editor');
        
        
        // Prevent Cross site request forgery (CSRF) attack
        $this->addElement('hash', 'csrf_token',  
                    array('salt' => get_class($this) . 'ds38JHyw')); 
        
        
        // Submit input field
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setValue('Save');
        
        // Add elements to the form
        $this->addElements(array($firstName, $lastName, $username, $password, $passwdRepeat, $role, $submit));
      
    }
}

