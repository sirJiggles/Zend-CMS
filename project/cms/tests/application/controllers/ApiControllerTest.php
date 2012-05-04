<?php

/*
 * This is the controller for testing the api user controller in the system that
 * deals with all api user-esq functionality.
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Test_Controllers
 */

// Get an instance the user model file
require_once '../../api/application/models/Api.php';

class ApiControllerTest extends ControllerTestCase
{
    
    // We need the model from the api for util functions like get test users
    protected  $_apiModel;
    
    public function setUp(){
       // Set the parent up
       parent::setUp();
       // Get an instance of the user controller
       $this->_apiModel = new Application_Model_Api();
       
    }
    
    // Test that the index page exists
    public function testLandOnIndexPage()
    {
        $this->assertTrue(true);
        $this->dispatch('/api');
        $this->assertRedirect('/api/manage');
    }
    
    // Test to make sure editors are not able to access the user section
    public function testEditorNoAccessAdmin(){
        // Lets first login as an editor
        $this->loginEditor();
        
        // Now we will attempt to access the api user admin section
        $this->dispatch('/api/manage');
        $this->assertRedirectTo('/error/not-the-droids');
    }
    
    // Test to make sure the admin users can see the user manage section
    public function testAdminHasAccessAdmin(){
        // should be admin as default so just check the dispatch
        $this->dispatch('/api/manage');
        $this->assertNotRedirect();
        $this->assertController('api');
        $this->assertAction('manage');
        $this->assertResponseCode(200);
    }
    
    // Test that we can add a api user to the system as admin correctly
    public function testAddUserCorrect(){
        /* 
         * First we we will remove the test API account from the system
         */
        // Spoof the new user details
        $this->request->setMethod('POST')
             ->setPost($this->_getUserOneFormData());
        
        // Make sure we are in the right place and have the right things on screen
        $this->dispatch('/api/add');
        $this->assertAction('add');
        
        // Assert that we are redirected to the user manage screen, thus added
        $this->assertRedirectTo('/api/manage');
    }
    
    /*
     * We need to add a second api user to the system for testing some edit
     * functionality
     */
    public function testAddSecondUser(){
        $sampleData = $this->_getUserTwoFormData();

        $this->request->setMethod('POST')
             ->setPost($sampleData);
        // Make sure we are in the right place and have the right things on screen
        $this->dispatch('/api/add');
        $this->assertAction('add');
        // Assert that we are redirected to the user manage screen, thus added
        $this->assertRedirectTo('/api/manage'); 
        
    }

    
    // Test required inpts for the add / edit user form
    public function testRequiredInputsApiForm(){
        $sampleData = $this->_getUserOneFormData();
        $sampleData['ref'] = '';
        $sampleData['key'] = '';
        
        $this->request->setMethod('POST')
            ->setPost($sampleData);
        
        // Post the data to the following location
        $this->dispatch('/api/add');
        $this->assertAction('add');
        
        // Check our error messages on the screen
        $this->assertQueryContentContains('ul.errors li', 'Ref is required');
        $this->assertQueryContentContains('ul.errors li', 'Key is required');

    }
    
    /*
     * By the time we get to this test we should have added our test user so now
     * we just need to try and add it again with the same ref and test the error message
     */
    public function testRefTakenAdd(){
        
        $fakeDetails = $this->_getUserOneFormData();
        $fakeDetails['ref'] = 'apiUserTwo';
        $fakeDetails['key'] = 'Some random key';
       
        $this->request->setMethod('POST')
             ->setPost($fakeDetails);
        
        // Post the data to the following location
        $this->dispatch('/api/add');
        $this->assertAction('add');
        
        $this->assertQueryContentContains('.ui-state-highlight p', 'That ref is taken, please try again');
    }
    
     /*
     * As above we are now going to test the key taken functionality
     */
    public function testKeyTakenAdd(){
        
        $fakeDetails = $this->_getUserOneFormData();
        $fakeDetails['key'] = 'userTwoKey';
        $fakeDetails['ref'] = 'Some random ref';
       
        $this->request->setMethod('POST')
             ->setPost($fakeDetails);
        
        // Post the data to the following location
        $this->dispatch('/api/add');
        $this->assertAction('add');
        
        $this->assertQueryContentContains('.ui-state-highlight p', 'That key is taken, please try again');
        
    }
    
    /*
     * As we have already checked the validation of the api user from
     * we can now just focus on checking if we can actually edit
     * the api user.
     */
    public function testEditUser(){
        
        // We have to get the user from the model as we need the id in the uri
        $testUser = $this->_getTestUserOne();
        
        
        $sampleData = $this->_getUserOneFormData();
        $sampleData['key'] = 'changeUserOne';
        $sampleData['type'] = 2;
        
        $this->request->setMethod('POST')
             ->setPost($sampleData);
        
        // Post the data to the following location
        $this->dispatch('/api/edit/id/'.$testUser->id);
        $this->assertAction('edit');
        
        // Test that taken to manage page (where flash message is displayed)
        $this->assertRedirectTo('/api/manage');
        
        
        
    }
    
    public function testResetUserOne(){
        
        // We have to get the user from the model as we need the id in the uri
        $testUser = $this->_getTestUserOne();
        
        
        // Now reset user one details (as we will need them again later)
        $sampleData = $this->_getUserOneFormData();
        
        $this->request->setMethod('POST')
             ->setPost($sampleData);
        
        // Post the data to the following location
        $this->dispatch('/api/edit/id/'.$testUser->id);
        $this->assertAction('edit');
        
        // Test that taken to manage page (where flash message is displayed)
        $this->assertRedirectTo('/api/manage');
    }
    
    
    /*
    * By the time we get to this test we should have added our test user so now
    * we just need to try and add it again and test the error message
    */
    public function testRefTakenEdit(){
        
        // Get second test user object
        $testUser = $this->_getTestUserTwo();
        
        // Change the sample data username to be the same as the second test user
        $sampleData = $this->_getUserTwoFormData();
        $sampleData['ref'] = 'apiUserOne';
        
        $this->request->setMethod('POST')
             ->setPost($sampleData);
        
        // Post edit to edit the firs test users account, should throw error!
        $this->dispatch('/api/edit/id/'.$testUser->id);
 
        $this->assertAction('edit');
        
        $this->assertQueryContentContains('.ui-state-highlight p', 'That ref is taken, please try again');
    }
    
    /*
     * Test the email address taken functionality for edit user form
     */
    public function testKeyTakenEdit(){
        // Get second test user object
        $testUser = $this->_getTestUserTwo();
        
        // Change the sample data emaik address to be the same as the second test user
        $sampleData = $this->_getUserTwoFormData();
        $sampleData['key'] = 'userOneKey';
        
        $this->request->setMethod('POST')
             ->setPost($sampleData);
        
        
        // Post edit to edit the firs test users account, should throw error!
        $this->dispatch('/api/edit/id/'.$testUser->id);
 
        $this->assertAction('edit');
        
        $this->assertQueryContentContains('.ui-state-highlight p', 'That key is taken, please try again');
    }
    
    // Test incorrect request to edit user action
    public function testIncorrectArgsEdit(){
        $this->dispatch('/api/edit/id/sdasd');
        $this->assertRedirectTo('/api/manage');
    }
    
    // Test user not found given correct args (hopefully no one will have this id)
    public function testApiUserNotFoundEdit(){
        $this->dispatch('/api/edit/id/9999999999999999999999');
        $this->assertRedirectTo('/api/manage');
    }
    
    // Test unable to find user to remove by git params
    public function testCantLocateRemoveUserId(){
        $this->dispatch('/api/remove/id/9999999999999999999999');
        $this->assertRedirectTo('/api/manage');
        
        // Make sure user has not been removed
        $userObject = $this->_getTestUserTwo();
        $this->assertEquals('apiUserTwo', $userObject->ref, 'User two has been removed for some reason!');
    }
    
    // Test unable to find user to remove by git params
    public function testCantRemoveUserIdIncorrect(){
        $this->dispatch('/api/remove/id/fsdfdssf');
        $this->assertRedirectTo('/api/manage');
        
        // Make sure user has not been removed
        $userObject = $this->_getTestUserTwo();
        $this->assertEquals('apiUserTwo', $userObject->ref, 'User two has been removed for some reason!');
    }
    
    
    // Test the remove user action on our second test user account
    public function testRemoveUser(){
        
        $userObject = $this->_getTestUserTwo();
        if ($userObject != null){
            $this->dispatch('/api/remove/id/'.$userObject->id);
            $this->assertRedirectTo('/api/manage');
            
            // Test the user has been removed from the db
            $userObject = $this->_getTestUserTwo();
            $this->assertEquals(null, $userObject, 'User Two has not been removed');
        }
    }
    
    // Test the remove user confirm action
    public function testRemoveUserConfirmAction(){
        $this->dispatch('/api/remove-confirm');
        $this->assertRedirect('/api/manage');
    }
    
    // Test the remove user confirm action with an actual user id
    public function testRemoveUserConfirmWithId(){
        $userOne = $this->_getTestUserOne();
        $this->dispatch('/api/remove-confirm/id/'.$userOne->id);
        $this->assertResponseCode(200);
        
        // Now really remove it
        $this->_removeTestApiUserOne();
    }
    
    // Test remove user confirm action with incorrect ID
    public function testRemoveUserConfirmIncorrectId(){
        $this->dispatch('/api/remove-confirm/id/999999999999999');
        $this->assertRedirect('/api/manage');
    }
   
   
    /*
     * This is a utils function for this controller that will remove the 
     * php unit test user from the database as if previous tests failed
     * and the test user still existed, future test will fail even if correct
     * as you cannot add duplicate users to the system
     */
    
    protected function _removeTestApiUserOne(){
        // Remove first test user
        $userObject = $this->_apiModel->getUserByRef("apiUserOne");
        if (!is_string($userObject)){
            $this->_apiModel->removeUser($userObject->id);
        }
    }
    
    /*
     * This utils function just grabs the first test user form the system
     * 
     * @return Zend_Db_Table_Row $userObject
     */
    protected function _getTestUserOne(){
        $userObject = $this->_apiModel->getUserByRef("apiUserOne");
        if (!is_string($userObject)){
            return $userObject;
        }else{
            return null;
        }
    }
    
    /*
     * This utils function just grabs the second test user form the system
     * 
     * @return Zend_Db_Table_Row $userObject
     */
    protected function _getTestUserTwo(){
        $userObject = $this->_apiModel->getUserByRef("apiUserTwo");
        if (!is_string($userObject)){
            return $userObject;
        }else{
            return null;
        }
    }
    
    /*
     * As we use an array of user data to test the edit and add user form 
     * we can call it from here rather than use it multiple times
     * 
     * @return array @sampleData
     */
    protected function _getUserOneFormData(){
        $sampleData = array(
            'ref' => 'apiUserOne',
            'key' => 'userOneKey',
            'type' => 1
        );
        return $sampleData;
    }
    
    /*
     * As we use an array of user data to test the edit and add user form 
     * we can call it from here rather than use it multiple times
     * 
     * @return array @sampleData
     */
    protected function _getUserTwoFormData(){
        $sampleData = array(
            'ref' => 'apiUserTwo',
            'key' => 'userTwoKey',
            'type' => 1
        );
        return $sampleData;
    }
    

}
