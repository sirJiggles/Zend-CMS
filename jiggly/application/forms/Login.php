<?php

class Application_Form_Login extends Zend_Form
{

    public function init()
    {
        // Username input field
        $username = new Zend_Form_Element_Text('username');
        $username->addFilter('StringTrim')
                ->setRequired(true)
                ->addErrorMessage('Username is required')
                ->setLabel('Username:');
        
        // Password input field
        $password = new Zend_Form_Element_Password('password');
        $password->setRequired(true)
                ->addErrorMessage('Password is required')
                ->setLabel('Password:');
        
        // Submit input field
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setValue('Login');
        
        // Add elements to the form
        $this->addElements(array($username, $password, $submit));
      
    }
}

