<?php

/*
 * This is the form for changing the users password in the system
 * 
 * Gareth Fuller
 */

class Application_Form_ChangePassword extends Zend_Form
{
    
    public function init()
    {
        $this->setAttrib('class', 'user');
        
        $customDecorators = array(
                            'ViewHelper',
                            'Description',
                            'Errors',
                            array(array('Input' => 'HtmlTag'), array('tag' => 'dd')),
                            array('Label', array('tag' => 'dt')),
                            array(array('row' => 'HtmlTag'), array('tag' => 'div', 'class' => 'item-wrapper')));
        

        // Password input field
        $password = new Zend_Form_Element_Password('password');
        $password->addErrorMessage('Password is required')
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->setDecorators($customDecorators)
                ->addValidator('Identical', false, array('token' => 'password_repeat'))
                ->setLabel('Password');
        
        // Password repeat field
        $passwdRepeat = new Zend_Form_Element_Password('password_repeat');
        $passwdRepeat->addErrorMessage('Passwords don\'t match')
                ->addValidator('Identical', false, array('token' => 'password'))
                ->setDecorators($customDecorators)
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->setLabel('Reapeat Password');
        
        
        // Hidden input field for the users forgot password hash
        $hash = new Zend_Form_Element_Hidden('hash');
        
        
        
        // Submit input field
        $submit = new Zend_Form_Element_Submit('Request');
        $submit->setValue('Request new password')
                ->setAttrib('class', 'button');
        
        $this->addElements(array($password, $passwdRepeat, $hash, $submit));

        
    }
}

