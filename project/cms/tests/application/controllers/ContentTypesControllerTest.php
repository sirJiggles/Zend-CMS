<?php

/*
 * This is the controller for testing the content types fucntionality
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Test_Controllers
 */

// Get an instance the content types model file
require_once '../../api/application/models/ContentTypes.php';

class ContentTypesControllerTest extends ControllerTestCase
{
    
    // We need the model from the api for util functions like get test content types
    protected  $_contentTypesModel;
    
    public function setUp(){
       // Set the parent up (disable the admin login)
       parent::setUp();
       // Get an instance of the content types model
       $this->_contentTypesModel = new Application_Model_ContentTypes();
       
       $this->loginSuperAdmin();

    }
    
    // Test that the index page exists
    public function testLandOnIndexPage()
    {
        $this->dispatch('/contenttypes');
        $this->assertNotRedirect();
        $this->assertController('contenttypes');
        $this->assertAction('index');
        $this->assertResponseCode(200);
        
    }
    
    // Test to make sure editors cannot get to this section
    public function testEditorNoAccess(){
        // Lets first login as an editor
        $this->loginEditor();
        
        // Now we will attempt to access the content types section
        $this->dispatch('/contenttypes');
        $this->assertRedirectTo('/error/not-the-droids');
    }
    
    // Test to make sure admins cannot get to this section
    public function testAdminNoAccess(){
        // Lets first login as an editor
        $this->loginAdmin();
        
        // Now we will attempt to access the content types section
        $this->dispatch('/contenttypes');
        $this->assertRedirectTo('/error/not-the-droids');
    }
    
   
    // Test that we can add a content type to the system
    public function testAddCorrect(){
        
        $this->loginSuperAdmin();
        
        // Spoof the new user details
        $this->request->setMethod('POST')
             ->setPost(array('name' => 'TestContentTypeOne'));
        
        // Make sure we are in the right place and have the right things on screen
        $this->dispatch('/contenttypes/add');
        $this->assertAction('add');
        
        // Assert that we are redirected to the contenttypes screen, thus added
        $this->assertRedirectTo('/contenttypes');
        
    }
    
    // Test required inputs
    public function testRequiredInputs(){
        
        $this->request->setMethod('POST')
            ->setPost(array('name' => ''));
        
        // Post the data to the following location
        $this->dispatch('/contenttypes/add');
        $this->assertAction('add');
        
        // Check our error messages on the screen
        $this->assertQueryContentContains('ul.errors li', 'Name is required');

    }
    

    /*
     * By the time we get here we sould have added a content type so 
     * now we will try to add one with the same name to make sure we cant
     */
    public function testNameTakenAdd(){
        
        
        $this->request->setMethod('POST')
             ->setPost(array('name' => 'TestContentTypeOne'));
        
        // Post the data to the following location
        $this->dispatch('/contenttypes/add');
        $this->assertAction('add');
        
        $this->assertRedirectTo('/contenttypes');
        
        // Then lets make sure its not been renamed
        $contentTypeApi = $this->_contentTypesModel->getContentTypeByName('TestContentTypeOne');
        $this->assertNotNull($contentTypeApi, 'The item by the original name could not be found name check failing!');
        
    }
    
    /*
     * Test that we can edit a content type name
     */
    public function testEditUser(){
       
        $this->request->setMethod('POST')
             ->setPost(array('name' => 'RenameTestContentType'));
        
        // get the content type form the api so we can get the id
        $contentTypeApi = $this->_contentTypesModel->getContentTypeByName('TestContentTypeOne');
        
        // Post the data to the following location
        $this->dispatch('/contenttypes/edit/id/'.$contentTypeApi->id);
        $this->assertAction('edit');
        
        // Test that taken to manage page (where flash message is displayed)
        $this->assertRedirectTo('/contenttypes');
    }
    
    
    // Test incorrect request to edit content type action
    public function testIncorrectArgsEdit(){
        $this->dispatch('/contenttypes/edit/id/sdasd');
        $this->assertRedirectTo('/contenttypes');
    }
    
    // Test content type not found given correct args (hopefully no one will have this id)
    public function testContentTypeNotFoundEdit(){
        $this->dispatch('/contenttypes/edit/id/9999999999999999999999');
        $this->assertRedirectTo('/contenttypes');
    }
    
    
    // Test unable to find content type to remove by git params
    public function testCantLocateRemoveUserId(){
        $this->dispatch('/contenttypes/remove/id/9999999999999999999999');
        $this->assertRedirectTo('/contenttypes');
        
        // Make sure the test content type has not beed removed
        $contentTypeApi = $this->_contentTypesModel->getContentTypeByName('RenameTestContentType');
        $this->assertEquals('RenameTestContentType', $contentTypeApi->name, 'Content Type has been removed for some reason!');
    }
    
    // Test the remove datatype confirm action
    public function testRemoveConfirmAction(){
        $this->dispatch('/contenttypes/remove-confirm');
        $this->assertRedirectTo('/contenttypes');
    }
    
    // Test the remove datatype confirm action with an actual type id
    public function testRemoveConfirmWithId(){
        $contentTypeApi = $this->_contentTypesModel->getContentTypeByName('RenameTestContentType');
        $this->dispatch('/contenttypes/remove-confirm/id/'.$contentTypeApi->id);
        $this->assertResponseCode(200);
    }
    
    // Test remove datatype confirm action with incorrect ID
    public function testRemoveConfirmInccorectId(){
        $this->dispatch('/contenttypes/remove-confirm/id/999999999999999');
        $this->assertRedirectTo('/contenttypes');
    }
    
    
    // Here we test that we can remove the test content type
    public function testRemoveContentType(){
        $contentTypeApi = $this->_contentTypesModel->getContentTypeByName('RenameTestContentType');
        if ($contentTypeApi != null){
            $this->dispatch('/contenttypes/remove/id/'.$contentTypeApi->id);
            $this->assertRedirectTo('/contenttypes');
            
            // Test the content type has been removed from the db
            $contentTypeApi = $this->_contentTypesModel->getContentTypeByName('RenameTestContentType');
            $this->assertEquals(null, $contentTypeApi, 'Content Type has not been removed!');
        }
    }
    

}
