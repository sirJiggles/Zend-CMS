<?php

class UserController extends Zend_Test_PHPUnit_ControllerTestCase
{
    // Test the logout action
    public function testLogoutAction(){
        $this->dispatch('/user/logout');
        $this->assertAction('logout');
        $this->assertRedirect();
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
    public function testLoginPageHasCorrectForm(){
        $this->dispatch('/user/login');
        $this->assertAction('login');
        $this->assertQueryCount('form#login', 1);
    }
    

}