<?php

/*
 * This is the controller for testing the user controller in the system that
 * deals with all useresq functionality.
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Test_Controllers
 */

// Get an instance the user model file
require_once '../application/models/User.php';

class UserControllerTest extends ControllerTestCase
{
    
    // The variable to hold out model instance
    protected  $_userModel;
    
    public function setUp(){
       // Set the parent up
       parent::setUp();
       // Get an instance of the user controller
       $this->_userModel = new Application_Model_User();
       
    }
    
    /*
     * Test that the index page exists
     */
    public function testLandOnIndexPage()
    {
        $this->assertTrue(true);
        $this->dispatch('user');
        $this->assertController('user');
        $this->assertAction('index');
    }
    
    /*
     * Check there is the correct form on the login page
     */
    public function testLoginPage(){
        $this->dispatch('/user/login');
        $this->assertAction('login');
        // Check that there is a login for on this page
        $this->assertQueryCount('form#loginForm', 1, 'Unable to locate the login form');
        
        $loginForm = new Application_Form_Login();
        $this->assertInstanceOf('Zend_Form', $loginForm);
    }
    
    /*
     * Check that with the correct details we can login
     */
    public function testLoginCorrect(){
        
        //first logout of the system
        $this->logout();
        
        // Spoof the login of correct details
        $this->request->setMethod('POST')
              ->setPost(array(
                  'username' => 'gfuller',
                  'password' => 'monkey12'
              ));
        $this->dispatch('/user/login');
        
        // Check that we are redirected to the correct place
        $this->assertRedirectTo('/');
        
    }
    
    
    
    
    

    /*
    // Test the logout action
    public function testLogoutAction(){
        $this->dispatch('/user/logout');
        $this->assertAction('logout');
        $this->assertRedirect('/');
    }
    
    // Test users with the right creds are logged in correctly
    public function testCorrectLogin(){
        
        $this->request->setMethod('POST')
              ->setPost(array(
                  'username' => 'gfuller',
                  'password' => 'monkey12'
              ));
        $this->dispatch('/user/login');
        $this->assertRedirectTo('/');
 
        $this->resetRequest()
             ->resetResponse();
        
    }
    
    // Test the login page contains the correct form
   
    */

}
