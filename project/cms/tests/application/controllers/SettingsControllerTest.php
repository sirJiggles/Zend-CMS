<?php

/*
 * This is the test controller that deals with all the settings functionality
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Test_Controllers
 */

// Get an instance the user model file
//require_once '../application/models/User.php';

class SettingsControllerTest extends ControllerTestCase
{
    
    // The variable to hold out model instance
    //protected  $_userModel;
    
    public function setUp(){
       // Set the parent up
       parent::setUp();
       // Get an instance of the user controller
       //$this->_userModel = new Application_Model_User();
       
    }
    
    // Test that the index page exists
    public function testLandOnIndexPage()
    {
        $this->assertTrue(true);
        $this->dispatch('settings');
        $this->assertController('settings');
        $this->assertAction('index');
        $this->assertResponseCode(200);
    }
    
    
    // Test to make sure an editor cannot reach this page
    public function testEditorNoAccess(){
        // Lets first login as an editor
        $this->loginEditor();
        
        // Now we will attempt to access the user admin section
        $this->dispatch('/settings');
        $this->assertRedirectTo('/error/not-the-droids');
    }

    

}
