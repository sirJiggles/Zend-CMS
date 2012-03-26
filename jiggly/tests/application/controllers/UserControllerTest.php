<?php

/*
 * This is the controller for testing the user controller in the system that
 * deals with all user-esq functionality.
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
    
    // Test that the index page exists
    public function testLandOnIndexPage()
    {
        $this->assertTrue(true);
        $this->dispatch('user');
        $this->assertController('user');
        $this->assertAction('index');
        $this->assertResponseCode(200);
    }
    
    // Check there is the correct form on the login page
    public function testLoginPage(){
        $this->dispatch('/user/login');
        $this->assertAction('login');
        // Check that there is a login for on this page
        $this->assertQueryCount('form#loginForm', 1, 'Unable to locate the login form');
        
        $loginForm = new Application_Form_Login();
        $this->assertInstanceOf('Zend_Form', $loginForm);
    }
    
    // Check that with the correct details we can login
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
    
    // Check the user is dealt with correctly for incorrect login
    public function testIncorrectLogin(){
        $this->logout();
        
        // Spoof the login of correct details
        $this->request->setMethod('POST')
              ->setPost(array(
                  'username' => 'gfuller',
                  'password' => 'someincorrectpassword'
              ));
        $this->dispatch('/user/login');
        
        // Check they are shown the "incorrect login message"
        $this->assertQueryContentContains('.ui-state-highlight p', 'Login details incorrect');
    }
    
    // Test to make sure editors are not able to access the user section
    public function testEditorNoAccessAdmin(){
        // Lets first login as an editor
        $this->loginEditor();
        
        // Now we will attempt to access the user admin section
        $this->dispatch('/user/manage');
        $this->assertRedirectTo('/error/not-the-droids');
    }
    
    // Test to make sure the admin users can see the user manage section
    public function testAdminHasAccessAdmin(){
        // should be admin as default so just check the dispatch
        $this->dispatch('/user/manage');
        $this->assertNotRedirect();
        $this->assertController('user');
        $this->assertAction('manage');
        $this->assertResponseCode(200);
    }
    
    // Test that we can add a user to the system as admin correctly
    public function testAddUserCorrect(){
        
        /* 
         * First we will remove the user from the db (if they are there) as
         * if previous tests failed the old user may still exist and this
         * step wil fail even if correct
         */
        $this->_removeTestUser();
        
        // Spoof the new user details
        $this->request->setMethod('POST')
             ->setPost($this->_getUserFormSampleData());
        
        // Make sure we are in the right place and have the right things on screen
        $this->dispatch('/user/add');
        $this->assertAction('add');
        
        // Assert that we are redirected to the user manage screen, thus added
        $this->assertRedirectTo('/user/manage');
    }
    
    // Test required inpts for the add / edit user form
    public function testRequiredInputsUserForm(){
        
        $sampleData = $this->_getUserFormSampleData();
        $sampleData['last_name'] = '';
        $sampleData['password'] = '';
        $sampleData['first_name'] = '';
        $sampleData['username'] = '';
        $sampleData['password_repeat'] = '';
        $this->request->setMethod('POST')
            ->setPost($sampleData);
        
        // Post the data to the following location
        $this->dispatch('/user/add');
        $this->assertAction('add');
        
        // Check our error messages on the screen
        $this->assertQueryContentContains('ul.errors li', 'Last name is required');
        $this->assertQueryContentContains('ul.errors li', 'Password is required');
        $this->assertQueryContentContains('ul.errors li', 'Username is required');
        $this->assertQueryContentContains('ul.errors li', 'First name is required');
        $this->assertQueryContentContains('ul.errors li', 'Passwords don\'t match');
        
    }
    
    // Test the error message for when the asswords don't match
    public function testPasswordsDontMatch(){
        $sampleData = $this->_getUserFormSampleData();
        $sampleData['password'] = 'somediff';
        $this->request->setMethod('POST')
             ->setPost($sampleData);
        
        // Post the data to the following location
        $this->dispatch('/user/add');
        $this->assertAction('add');
        
        $this->assertQueryContentContains('ul.errors li', 'Passwords don\'t match');
    }
    
    /*
     * By the time we get to this test we should have added our test user so now
     * we just need to try and add it again and test the error message
     */
    public function testUserNameTaken(){
        
        $this->request->setMethod('POST')
             ->setPost($this->_getUserFormSampleData());
        
        // Post the data to the following location
        $this->dispatch('/user/add');
        $this->assertAction('add');
        
        $this->assertQueryContentContains('.ui-state-highlight p', 'That username is taken, please try again');
    }
    
    /*
     * As we have already checked the validation of the user from
     * we can now just focus on checking if we can actually edit
     * the user.
     */
    public function testEditUser(){
        $sampleData = $this->_getUserFormSampleData();
        $sampleData['password'] = 'changed';
        $sampleData['password_repeat'] = 'changed';
        $sampleData['role'] = 'editor';
        $sampleData['active'] = 0;
        $sampleData['first_name'] = 'Unit';
        $sampleData['last_name'] = 'Edited';
        
        $this->request->setMethod('POST')
             ->setPost($sampleData);
        
        // We have to get the user from the model as we need the id in the uri
        $testUser = $this->_getTestUser();
        
        // Post the data to the following location
        $this->dispatch('/user/edit/id/'.$testUser->id);
        $this->assertAction('edit');
        
        // Test that taken to manage page (where flash message is displayed)
        $this->assertRedirectTo('/user/manage');
    }
    
    
    /*
     * This is a utils function for this controller that will remove the 
     * php unit test user from the database as if previous tests failed
     * and the test user still existed, future test will fail even if correct
     * as you cannot add duplicate users to the system
     */
    protected function _removeTestUser(){
        $userObject = $this->_userModel->getUserByUsername('phpUnitTestUser');
        
        if ($userObject != null){
            $this->_userModel->removeUser($userObject->id);
        }
    }
    
    /*
     * This utils function just grabs the test user form the system
     * 
     * @return Zend_Db_Table_Row $userObject
     */
    protected function _getTestUser(){
        $userObject = $this->_userModel->getUserByUsername('phpUnitTestUser');
        if ($userObject != null){
            return $userObject;
        }
    }
    
    /*
     * As we use an array of user data to test the edit and add user form 
     * we can call it from here rather than use it multiple times
     * 
     * @return array @sampleData
     */
    protected function _getUserFormSampleData(){
        $sampleData = array(
            'username' => 'phpUnitTestUser',
            'password' => 'phpUnitTestPassword',
            'password_repeat' => 'phpUnitTestPassword',
            'first_name' => 'PHP',
            'last_name' => 'UNIT',
            'role' => 'admin'
        );
        return $sampleData;
    }
    

}
