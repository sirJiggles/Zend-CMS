<?php

/*
 * This is the forgotten password form for the system
 * 
 * Gareth Fuller
 */

class Application_Form_ForgotPassword extends Zend_Form
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
        

        // Email input field
        $email = new Zend_Form_Element_Text('email');
        $email->setRequired(true)
                ->addFilter(new Zend_Filter_HtmlEntities())
                ->addValidator(new Zend_Validate_EmailAddress())
                ->addErrorMessage('Email Address is required')
                ->setDecorators($customDecorators)
                ->setLabel('Email address:');
        
        // Submit input field
        $submit = new Zend_Form_Element_Submit('Request');
        $submit->setValue('Request new password')
                ->setAttrib('class', 'button');
        
        $this->addElements(array($email, $submit));

        
    }
}

