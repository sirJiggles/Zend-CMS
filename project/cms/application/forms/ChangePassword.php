<?php

/*
 * This is the form for chaning the users password in the system
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Forms
 */

class Application_Form_ChangePassword extends Zend_Form
{
    
    public function init()
    {
        $this->setAttrib('class', 'user');
        $this->setAttrib('id', 'changePassword');
        
        $customDecorators = array(
                            'ViewHelper',
                            'Description',
                            'Errors',
                            array(array('Input' => 'HtmlTag'), array('tag' => 'dd')),
                            array('Label', array('tag' => 'dt')),
                            array(array('row' => 'HtmlTag'), 
                                  array('tag' => 'div', 'class' => 'item-wrapper')));
        

        // Password input field
        $password = new Zend_Form_Element_Password('password');
        $password->addErrorMessage('Password is required')
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->setDecorators($customDecorators)
                ->addValidator('Identical', FALSE, array('token' => 'password_repeat'))
                ->setLabel('Password');
        
        // Password repeat field
        $passwdRepeat = new Zend_Form_Element_Password('password_repeat');
        $passwdRepeat->addErrorMessage('Passwords don\'t match')
                ->addValidator('Identical', FALSE, array('token' => 'password'))
                ->setDecorators($customDecorators)
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->setLabel('Reapeat Password');
        
        
        // Hidden input field for the users forgot password hash
        $hash = new Zend_Form_Element_Hidden('hash');
        
        
        
        // Submit input field
        $submit = new Zend_Form_Element_Submit('Request');
        $submit->setValue('Request new password')
                ->setAttrib('data-theme', 'a');
        
        $this->addElements(array($password, $passwdRepeat, $hash, $submit));

        
    }
}

