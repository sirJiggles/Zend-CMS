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

        

        // Captcha input (only show if 3 incorrect loggins)
        $captcha_session = new Zend_Session_Namespace('captcha');
 
        if ($captcha_session->tries > 2)
        {
            $privatekey = '6Lc-cs4SAAAAAJ-YKJtdlYoGLGPKIFGP3BcALePE';
            $publickey = '6Lc-cs4SAAAAAPjbR9_bXExC7e3OKHaMeAdrijkp';

            $recaptcha = new Zend_Service_ReCaptcha($publickey, $privatekey);
            $recaptcha->setOption('theme', 'clean');

            $captcha = new Zend_Form_Element_Captcha('captcha',
                array(
                    'captcha'       => 'ReCaptcha',
                    'captchaOptions' => array('captcha' => 'ReCaptcha', 'service' => $recaptcha),
                    'ignore' => true
                    )
            );

            $this->addElements(array($username, $password, $captcha, $submit));
        }else{
            // Add the standard elements to the user form
            $this->addElements(array($username, $password, $submit));
        }
        
        
        
    }
}

