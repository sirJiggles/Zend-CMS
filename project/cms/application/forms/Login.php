<?php

/*
 * This is the main login form for the system
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Forms
 */

class Application_Form_Login extends Zend_Form
{
    
    public function init()
    {
        $this->setAttrib('class', 'login');
        $this->setAttrib('id', 'loginForm');
        
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
        
        $action = new Zend_Form_Element_Hidden('action');
        $action->setValue('add');
        
        // Submit input field
        $submit = new Zend_Form_Element_Submit('Login');
        $submit->setValue('Login')
                ->setAttrib('data-theme', 'e')
                ->setAttrib('class', 'submit');
        
        $this->addElements(array($username, $password, $submit, $action));

        
    }
}

