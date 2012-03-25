<?php

// Get the user controller from the application
//require_once '../application/controllers/UserController.php';

class UserControllerTest extends ControllerTestCase
{
    
    // The variable to hold out controller instance
    //protected  $userController;
    
   /* public function setUp(){
        parent::setUp();
        // Get an instance of the user controller
       //$this->userController = new UserController();
        
        // Set the parent up
    }*/
    
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
    
    public function testLoginPageHasCorrectForm(){
        $this->dispatch('/user/login');
        $this->assertAction('login');
        //$this->assertQueryCount('Form.login', 1);
        //$this->assertQ
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
