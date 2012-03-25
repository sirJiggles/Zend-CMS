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
        $this->setAttrib('class', 'login');
        //$this->setAttrib($key, $value)
        
        $customDecorators = array(
                            'ViewHelper',
                            'Description',
                            'Errors',
                            array(array('Input' => 'HtmlTag'), array('tag' => 'dd')),
                            array('Label', array('tag' => 'dt')),
                            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'item-wrapper')));
        
        
        // Username input field
        $username = new Zend_Form_Element_Text('username');
        $username->addFilter('StringTrim')
                ->setRequired(true)
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->addErrorMessage('Username is required')
                ->setDecorators($customDecorators)
                ->setLabel('Username:');
        
        // Password input field
        $password = new Zend_Form_Element_Password('password');
        $password->setRequired(true)
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->addErrorMessage('Password is required')
                ->setDecorators($customDecorators)
                ->setLabel('Password:');
        
        // Submit input field
        $submit = new Zend_Form_Element_Submit('Login');
        $submit->setValue('Login')
                ->setAttrib('class', 'button');
        
        $this->addElements(array($username, $password, $submit));

        
    }
}

