<?php

/*
 * This is the login form for the system
 * 
 * Gareth Fuller
 */

class Application_Form_Login extends Zend_Form
{
    
    public function init()
    {
        // Username input field
        $username = new Zend_Form_Element_Text('username');
        $username->addFilter('StringTrim')
                ->setRequired(true)
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->addErrorMessage('Username is required')
                ->setLabel('Username:');
        
        // Password input field
        $password = new Zend_Form_Element_Password('password');
        $password->setRequired(true)
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->addErrorMessage('Password is required')
                ->setLabel('Password:');
        
        // Prevent Cross site request forgery (CSRF) attack
        /*$this->addElement('hash', 'csrf_token',  
                    array('salt' => get_class($this) . 'ds38JHyw')); */
        
        // Submit input field
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setValue('Login');
        
        // Add elements to the form
        $this->addElements(array($username, $password, $submit));
      
    }
}

