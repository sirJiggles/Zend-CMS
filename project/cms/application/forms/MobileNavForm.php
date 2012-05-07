<?php

/*
 * This is the dropdown main nav for the mobile version of the site
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Forms
 */
class Application_Form_MobileNavForm extends Zend_Form
{
    
    public function init()
    {
       
        $this->setAttrib('class', 'nav-mobile');
        $this->setAttrib('id', 'main-nav-form');
        
        // Navigation input field
        $nav = new Zend_Form_Element_Select('nav');
        $nav->setLabel('Select page:')
                ->addMultiOption('index', 'Home')
                ->addMultiOption('content', 'Add Content')
                ->setAttrib('data-native-menu', 'false')
                ->setAttrib('data-theme', 'a');
       
        $this->addElements(array($nav));

    }
    
    
}

