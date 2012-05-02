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
require_once '../../api/application/models/User.php';

class UserControllerTest extends ControllerTestCase
{
    
    // We need the model from the api for util functions like get test users
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
        $this->dispatch('/user');
        $this->assertRedirect('/user/manage');
        
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
    public function testInCorrectLogin(){
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
        $this->_removeTestUsers();
        
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
        $sampleData['email_address'] = '';
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
        $this->assertQueryContentContains('ul.errors li', 'Email address is required');

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
    public function testUserNameTakenAdd(){
        
        $fakeDetails = $this->_getUserFormSampleData();
        $fakeDetails['email_address'] = 'adifferent@email.com';
       
        $this->request->setMethod('POST')
             ->setPost($fakeDetails);
        
        // Post the data to the following location
        $this->dispatch('/user/add');
        $this->assertAction('add');
        
        $this->assertQueryContentContains('.ui-state-highlight p', 'That username is taken, please try again');
    }
    
    /*
     * Test the email address taken functionality on adding users form
     */
    public function testUserEmailAddressTakenAdd(){
        $fakeDetails = $this->_getUserFormSampleData();
        $fakeDetails['username'] = 'someusernamethatshouldnotexist';
        
        $this->request->setMethod('POST')
             ->setPost($fakeDetails);
        
        // Post the data to the following location
        $this->dispatch('/user/add');
        $this->assertAction('add');
        
        $this->assertQueryContentContains('.ui-state-highlight p', 'That email address is taken, please try again');
        
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
        $testUser = $this->_getTestUserOne();
        
        // Post the data to the following location
        $this->dispatch('/user/edit/id/'.$testUser->id);
        $this->assertAction('edit');
        
        // Test that taken to manage page (where flash message is displayed)
        $this->assertRedirectTo('/user/manage');
    }
    
    /*
     * We need to add a second user to the system for testing some edit
     * functionality
     */
    public function testAddSecondUser(){
        $sampleData = $this->_getUserFormSampleData();
        $sampleData['username'] = 'PHPUnitUserTwo';
        $sampleData['email_address'] = 'phpunittwo@emailaddress.com';
        
        $this->request->setMethod('POST')
             ->setPost($sampleData);
        // Make sure we are in the right place and have the right things on screen
        $this->dispatch('/user/add');
        $this->assertAction('add');
        // Assert that we are redirected to the user manage screen, thus added
        $this->assertRedirectTo('/user/manage'); 
    }
    
    /*
    * By the time we get to this test we should have added our test user so now
    * we just need to try and add it again and test the error message
    */
    public function testUserNameTakenEdit(){
        
        // Change the sample data username to be the same as the second test user
        $sampleData = $this->_getUserFormSampleData();
        $sampleData['username'] = 'PHPUnitUserTwo';
        $sampleData['email_address'] = 'adifferent@email.com';
        
        $this->request->setMethod('POST')
             ->setPost($sampleData);
        
        // Get first test user object
        $testUser = $this->_getTestUserOne();
        
        // Post edit to edit the firs test users account, should throw error!
        $this->dispatch('/user/edit/id/'.$testUser->id);
 
        $this->assertAction('edit');
        
        $this->assertQueryContentContains('.ui-state-highlight p', 'That username is taken, please try again');
    }
    
    /*
     * Test the email address taken functionality for edit user form
     */
    public function testUserEmailAddressTakenEdit(){
        // Change the sample data emaik address to be the same as the second test user
        $sampleData = $this->_getUserFormSampleData();
        $sampleData['username'] = 'someusernamethatshouldnotexist';
        $sampleData['email_address'] = 'phpunittwo@emailaddress.com';
        
        $this->request->setMethod('POST')
             ->setPost($sampleData);
        
        // Get first test user object
        $testUser = $this->_getTestUserOne();
        
        // Post edit to edit the firs test users account, should throw error!
        $this->dispatch('/user/edit/id/'.$testUser->id);
 
        $this->assertAction('edit');
        
        $this->assertQueryContentContains('.ui-state-highlight p', 'That email address is taken, please try again');
    }
    
    // Test incorrect request to edit user action
    public function testIncorrectArgsEdit(){
        $this->dispatch('/user/edit/id/sdasd');
        $this->assertRedirectTo('/user/manage');
    }
    
    // Test user not found given correct args (hopefully no one will have this id)
    public function testUserNotFoundEdit(){
        $this->dispatch('/user/edit/id/9999999999999999999999');
        $this->assertRedirectTo('/user/manage');
    }
    
    // Test the logout action
    public function testLogout(){
        $this->dispatch('/user/logout');
        $this->assertRedirectTo('/');
    }
    
    // Test unable to find user to remove by git params
    public function testCantLocateRemoveUserId(){
        $this->dispatch('/user/remove/id/9999999999999999999999');
        $this->assertRedirectTo('/user/manage');
        
        // Make sure user has not been removed
        $userObject = $this->_getTestUserTwo();
        $this->assertEquals('PHPUnitUserTwo', $userObject->username, 'User two has been removed for some reason!');
    }
    
    // Test unable to find user to remove by git params
    public function testCantRemoveUserIdIncorrect(){
        $this->dispatch('/user/remove/id/fsdfdssf');
        $this->assertRedirectTo('/user/manage');
        
        // Make sure user has not been removed
        $userObject = $this->_getTestUserTwo();
        $this->assertEquals('PHPUnitUserTwo', $userObject->username, 'User two has been removed for some reason!');
    }
    
    
    // Test the remove user action on our second test user account
    public function testRemoveUser(){
        
        $userObject = $this->_getTestUserTwo();
        if ($userObject != null){
            $this->dispatch('/user/remove/id/'.$userObject->id);
            $this->assertRedirectTo('/user/manage');
            
            // Test the user has been removed from the db
            $userObject = $this->_getTestUserTwo();
            $this->assertEquals(null, $userObject, 'User Two has not been removed');
        }
    }
    
    // Test the remove user confirm action
    public function testRemoveUserConfirmAction(){
        $this->dispatch('/user/remove-confirm');
        $this->assertRedirect('/user/manage');
    }
    
    // Test the remove user confirm action with an actual user id
    public function testRemoveUserConfirmWithId(){
        $userOne = $this->_getTestUserOne();
        $this->dispatch('/user/remove-confirm/id/'.$userOne->id);
        $this->assertResponseCode(200);
    }
    
    // Test remove user confirm action with incorrect ID
    public function testRemoveUserConfirmInccorectId(){
        $this->dispatch('/user/remove-confirm/id/999999999999999');
        $this->assertRedirect('/user/manage');
    }
    
     // Test that after a few incorrect logins we are shown the forgot password link
    public function testShownForgotPasswordLink(){
        $this->logout();
        
        // Create an array of incorrect login details that we will try to break into the site with
        $fakeDetails = array('username' => 'gfuller', 'password' => 'someincorrectpassword');
        
        // Prepare our incorrect login details for the first time
        $this->request->setMethod('POST')
                    ->setPost($fakeDetails);
        $this->dispatch('/user/login');
        
        // Prepare our incorrect login details for the second time
        $this->request->setMethod('POST')
                    ->setPost($fakeDetails);
        $this->dispatch('/user/login');
        
        // Prepare our incorrect login details for the third time
        $this->request->setMethod('POST')
                    ->setPost($fakeDetails);
        $this->dispatch('/user/login');
        
        // Prepare our incorrect login details for the fourth time
        $this->request->setMethod('POST')
                    ->setPost($fakeDetails);
        $this->dispatch('/user/login');
        
        // Check the contents of the page contains the forgot password link
        $this->assertQueryContentContains('#content a', 'Forgotten account details?');
    }
    
    // Test that we can reach the forgot password action and it contains the correct data
    public function testForgotPasswordActionAndContent(){
        // Logout as we should be able to reach this test without being logged in
        $this->logout();
        $this->dispatch('/user/forgot-password');
        $this->assertResponseCode(200);
        $this->assertQueryCount('form#forgotPassword', 1, 'Unable to locate the forgotten password form');
        
    }
    
    // Test submitting the forgotten password form and not finding user account
    public function testForgotPasswordSubmitNoAccountFound(){
        $this->logout();
        // prpare the fake form details
        $fakeDetails = array('username' => 'phpUnitTestUser', 'email' => 'someincorrect@email.com');
        
        // Prepare our incorrect forgotten password details for the first time
        $this->request->setMethod('POST')
                    ->setPost($fakeDetails);
        
        // Dispatch the request
        $this->dispatch('/user/forgot-password');
        
        $this->assertQueryContentContains('.ui-state-highlight p', 'Unable to find that email address in the system');
        
    }
    
    // Test correctly submiting the forgot password form
    public function testForgotPasswordAccountFound(){
        $this->logout();
        // prpare the fake form details
        $fakeDetails = array('username' => 'phpUnitTestUser', 'email' => 'phpunit@email.com');
        
        // Prepare our incorrect forgotten password details for the first time
        $this->request->setMethod('POST')
                    ->setPost($fakeDetails);
        
        // Dispatch the request
        $this->dispatch('/user/forgot-password/');
        
        // Make sure we are redirected to the login screen
        $this->assertRedirect('/user/login');
        
    }
    
    // Test the the activate password action exists and is as we expect it to be
    public function testActivatePasswordAction(){
        // Should be able to access even if not logged in
        $this->logout();
        $this->dispatch('user/activate-password');
        // As we have not supplied a code we should be taken to the login page
        $this->assertRedirect('user/login');
        
    }
    
    // Test activating the password with an incorrect hash
    public function testActivatePasswordIncorrectHash(){
        $this->logout();
        // get the actual hash from the database but change the users id in the req
        $userDetails = $this->_getTestUserOne();
        $hash = ($userDetails->id + 1).$userDetails->forgot_password_hash;
        $this->dispatch('user/activate-password/'.$hash);
        $this->assertRedirect('user/login');
        
    }
    
    // Test that when we send a correct hash we are shown the chnage password form
    public function testActivatePasswordCorrectHash(){
        $this->logout();
        // get the actual hash from the database
        $userDetails = $this->_getTestUserOne();
        $hash = $userDetails->id.':'.$userDetails->forgot_password_hash;
        $this->dispatch('user/activate-password/req/'.$hash);

        // Make sure we have the form to update our password
        $this->assertQueryCount('form#changePassword', 1, 'Unable to locate the chnage password form');
    }
    
    // Now test the whole reseting password action and that it all worked
    public function testActivatePasswordComplete(){
        $this->logout();
        // get the actual hash from the database
        $userDetails = $this->_getTestUserOne();
        $hash = $userDetails->id.':'.$userDetails->forgot_password_hash;
        
        // Put together some fake form details
        $fakeDetails = array('password' => 'monkey12', 'password_repeat' => 'monkey12', 'hash' => $hash);
        $this->request->setMethod('POST')
                    ->setPost($fakeDetails);
        
        $this->dispatch('user/activate-password/');
        
        $this->assertRedirect('user/login');
        
        // Get the user details and make sure the account is now active
        $userDetails = $this->_getTestUserOne();
        $this->assertEquals(1, $userDetails->active);
    }
    
    /*
     * This is a utils function for this controller that will remove the 
     * php unit test user from the database as if previous tests failed
     * and the test user still existed, future test will fail even if correct
     * as you cannot add duplicate users to the system
     */
    protected function _removeTestUsers(){
        // Remove first test user
        $userObject = $this->_userModel->getUserByUsername('phpUnitTestUser');
        if ($userObject != null){
            $this->_userModel->removeUser($userObject->id);
        }
        
        // Remove second test user
        $userObject = $this->_userModel->getUserByUsername('PHPUnitUserTwo');
        if ($userObject != null){
            $this->_userModel->removeUser($userObject->id);
        }
    }
    
    /*
     * This utils function just grabs the first test user form the system
     * 
     * @return Zend_Db_Table_Row $userObject
     */
    protected function _getTestUserOne(){
        $userObject = $this->_userModel->getUserByUsername('phpUnitTestUser');
        if ($userObject != null){
            return $userObject;
        }
    }
    
    /*
     * This utils function just grabs the second test user form the system
     * 
     * @return Zend_Db_Table_Row $userObject
     */
    protected function _getTestUserTwo(){
        $userObject = $this->_userModel->getUserByUsername('PHPUnitUserTwo');
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
            'role' => 'editor',
            'active' => 0,
            'email_address' => 'phpunit@email.com'
        );
        return $sampleData;
    }
    

}
