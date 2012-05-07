<?php

/*
 * This is the controller for testing the data type fields functionality
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Test_Controllers
 */

// Get an instance the data types model file
require_once '../../api/application/models/DataTypeFields.php';

class DataTypeFieldsControllerTest extends ControllerTestCase
{
    
    // We need the model from the api for util functions like get test content types
    protected  $_dataTypeFieldsModel;
    
    public function setUp(){
       // Set the parent up (disable the admin login)
       parent::setUp();
       // Get an instance of the data type fields model
       $this->_dataTypeFieldsModel = new Application_Model_DataTypeFields();

       $this->loginSuperAdmin();
    }
    
    // This est should redirect the user to datatypes
    public function testNoParamsIndexPage()
    {
        $this->dispatch('/datatypefields');
        $this->assertRedirect('/datatypes');        
    }
    
    // Test the index page
    public function testIndexPage(){
       
        $this->dispatch('/datatypefields/index/id/1');

        $this->assertNotRedirect();
        $this->assertController('datatypefields');
        $this->assertAction('index');
        $this->assertResponseCode(200); 
    }
    
    // Test to make sure editors cannot get to this section
    public function testEditorNoAccess(){
        // Lets first login as an editor
        $this->loginEditor();
        
        // Now we will attempt to access the content types section
        $this->dispatch('/datatypefields/index/id/1');
        $this->assertRedirectTo('/error/not-the-droids');
    }
    
    // Test to make sure admins cannot get to this section
    public function testAdminNoAccess(){
        // Lets first login as an editor
        $this->loginAdmin();
        // Now we will attempt to access the content types section
        $this->dispatch('/datatypefields/index/id/1');
        $this->assertRedirectTo('/error/not-the-droids');
    }
    
   
    // Test that we can add a content type field to the system
    public function testAddCorrect(){
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost(array('name' => 'TestContentTypeField',
                             'format' => 'text',
                             'content_type' => '1'));

        // Make sure we are in the right place and have the right things on screen
        $this->dispatch('/datatypefields/add/content-type/1');
        
        $this->assertAction('add');
        
        // Assert that we are redirected to the data type fields manage screen, thus added
        $this->assertRedirectTo('/datatypefields/index/id/1');
        
        
        
    }
    
    
    
    // Test required inputs for content type fields
    public function testRequiredInputs(){
        
        $this->request->setMethod('POST')
            ->setPost(array('name' => ''));
        
        // Post the data to the following location
        $this->dispatch('/datatypefields/add/content-type/1');
        $this->assertAction('add');
        
        // Test error message
        $this->assertQueryContentContains('ul.errors li', 'Name is required');

    }
    

    /*
     * Now that we have added a content type field we are going to test to make
     * sure we cant add a field to this content type by the same name
     */
    public function testNameTakenAdd(){
        
        $this->request->setMethod('POST')
             ->setPost(array('name' => 'TestContentTypeField',
                             'format' => 'text',
                             'content_type' => '1'));

        // Make sure we are in the right place and have the right things on screen
        $this->dispatch('/datatypefields/add/content-type/1');
        
        $this->assertAction('add');
        
        $this->assertRedirectTo('/datatypefields/index/id/1');
        
        // Then lets make sure its not been renamed
        $contentFieldObj = $this->_dataTypeFieldsModel->getContentFieldByName('TestContentTypeField', 1);
        $this->assertNotNull($contentFieldObj, 'The item by the original name could not be found name check failing!');
 
    }
    
    /*
     * Now we are going to add a data field to the second content type for
     * later testing
     */
    public function testAddSecondCorrect(){
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost(array('name' => 'TestContentTypeFieldTwo',
                             'format' => 'text',
                             'content_type' => '2'));
        
        // Make sure we are in the right place and have the right things on screen
        $this->dispatch('/datatypefields/add/content-type/2');
        $this->assertAction('add');
        
        // Assert that we are redirected to the data type fields manage screen, thus added
        $this->assertRedirectTo('/datatypefields/index/id/2');
        
    }
    
    /*
     * Test that we can edit the first content field in the first content type
     */
    public function testEditFirstContentTypeField(){
        // First we need to know the ID of the content type field we want to edit
        $contentFieldObj = $this->_dataTypeFieldsModel->getContentFieldByName('TestContentTypeField', 1);
        $contentFieldId = $contentFieldObj->id;
                
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost(array('name' => 'TestContentTypeFieldRename',
                             'format' => 'text',
                             'content_type' => '1'));
        
        // Make sure we are in the right place and have the right things on screen
        $this->dispatch('/datatypefields/edit/id/'.$contentFieldId.'/content-type/1');
        
        $this->assertAction('edit');
        
        // Assert that we are redirected to the data type fields manage screen, thus edited
        $this->assertRedirectTo('/datatypefields/index/id/1');
    }
    
    /*
     * Test that when we now try to change the name of the first one to be the
     * name of the second it lets us as content type fields can have the same
     * name if in a different content type
     */
    public function testCanNameFieldSameAsFieldInOtherType(){

        // First we need to know the ID of the content type field we want to edit
        $contentFieldObj = $this->_dataTypeFieldsModel->getContentFieldByName('TestContentTypeFieldRename', 1);
        $contentFieldId = $contentFieldObj->id;
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost(array('name' => 'TestContentTypeFieldTwo',
                             'format' => 'text',
                             'content_type' => '1'));
        
        // Make sure we are in the right place and have the right things on screen
        $this->dispatch('/datatypefields/edit/id/'.$contentFieldId.'/content-type/1');
        $this->assertAction('edit');
        
        // Assert that we are redirected to the data type fields manage screen, thus edited
        $this->assertRedirectTo('/datatypefields/index/id/1');
    }
    
    /*
     * Test that we cant add a field to the first content type with the same 
     * name as the one that is currently in there 
     */
    public function testNameTakenOnFieldInContentType(){
        
        $this->request->setMethod('POST')
             ->setPost(array('name' => 'TestContentTypeFieldTwo',
                             'format' => 'text',
                             'content_type' => '1'));
        
        // Make sure we are in the right place and have the right things on screen
        $this->dispatch('/datatypefields/add/content-type/1');
        $this->assertAction('add');
        
        // Assert that we are redirected to the data type fields manage screen
        $this->assertRedirectTo('/datatypefields/index/id/1');
        
        /*
         * Get the items from this content type to make sure there are not
         * now teo with the same name
         */
        $contentFields = $this->_dataTypeFieldsModel->getDataFieldsForDataType(1);
        $duplicateFound = false;
        $namesFound = array();
        foreach($contentFields as $field){
            if (in_array($field->name, $namesFound)){
                $duplicateFound = true;
                break;
            }
            $namesFound[] = $field->name;
        }
        
        $this->assertFalse($duplicateFound, 'Duplicate name of field found for one content type!');
    }
    

    // Test incorrect request to edit content type action
    public function testIncorrectArgsEdit(){
        $this->dispatch('/datatypefields/edit/id/sdasd/content-type/1');
        $this->assertRedirectTo('/datatypes');
    }
    
    // Test the field is not found on edit action
    public function testContentFieldNotFound(){
        $this->dispatch('/datatypefields/edit/id/9999999999999999999999/content-type/1');
        $this->assertRedirectTo('/datatypes');
    }
    
    /*
     * Test that if you go to edit a content field even if you have the right
     * id but don't pass the content-type id you are redirected
     */
    public function testEditCorrectParamButNoContentType(){
        $contentFieldObj = $this->_dataTypeFieldsModel->getContentFieldByName('TestContentTypeFieldTwo', 1);
        $contentFieldId = $contentFieldObj->id;
        
        $this->dispatch('/datatypefields/edit/id/'.$contentFieldId);
        $this->assertRedirectTo('/datatypes');
    }
    
    /*
     * Test that we have not sent a content type to the add action and are 
     * redirected to the main manage page
     */
    public function testAddCorrectButNoContentType(){
        $this->dispatch('/datatypefields/add');
        $this->assertRedirectTo('/datatypes');
    }
    
    
    // Test unable to locate the field for removing the content type field
    public function testCantLocateRemoveUserId(){
        
        $this->dispatch('/datatypefields/remove/id/9999999999999999999999/content-type/1');
        $this->assertRedirectTo('/datatypefields/index/id/1');
    }
    
    // Test correct remove user id but no content-type passed
    public function testCorrectRemoveidNoContentType(){
        $contentFieldObj = $this->_dataTypeFieldsModel->getContentFieldByName('TestContentTypeFieldTwo', 1);
        $contentFieldId = $contentFieldObj->id;
        $this->dispatch('/datatypefields/remove/id/'.$contentFieldId);
        $this->assertRedirectTo('/datatypes');
    }
    
     // Test the remove datatype field confirm action no content type
    public function testRemoveConfirmActionNoContentType(){
        $this->dispatch('/datatypefields/remove-confirm');
        $this->assertRedirectTo('/datatypes');
    }
    
    // Test the action with no id param
    public function testRemoveConfirmActionNoId(){
        $this->dispatch('/datatypefields/remove-confirm/content-type/1');
        $this->assertRedirectTo('/datatypes');
    }
    
    // Test remove datatype field confirm action with incorrect ID
    public function testRemoveConfirmInccorectId(){
        $this->dispatch('/datatypefields/remove-confirm/content-type/1/id/999999999999999');
        $this->assertRedirectTo('/datatypes');
    }
    
    // Test the remove confirm action with a correct id
    public function testRemoveConfirmWithId(){
        $contentFieldObj = $this->_dataTypeFieldsModel->getContentFieldByName('TestContentTypeFieldTwo', 1);
        $this->dispatch('/datatypefields/remove-confirm/id/'.$contentFieldObj->id.'/content-type/1');
        $this->assertResponseCode(200);
        $this->assertNotRedirect();
    }
    

    /*
     * Here we check that we can actually remove the first content type
     */
    public function testRemoveContentTypeFieldOne(){
        $contentFieldObj = $this->_dataTypeFieldsModel->getContentFieldByName('TestContentTypeFieldTwo', 1);
        $contentFieldId = $contentFieldObj->id;

        $this->dispatch('/datatypefields/remove/id/'.$contentFieldId.'/content-type/1');
        $this->assertRedirectTo('/datatypefields/index/id/1');

        // Test the content type field has been removed from the db
        $contentFieldObj = $this->_dataTypeFieldsModel->getContentFieldByName('TestContentTypeFieldTwo', 1);
        $this->assertEquals(null, $contentFieldObj, 'Content Type Field has not been removed!');
        
    }
    
    /*
     * Now we clean up all the other test content type fields as we dont need them anymore
     */
    public function testCleanup(){
        $contentFieldObj = $this->_dataTypeFieldsModel->getContentFieldByName('TestContentTypeFieldTwo', 2);
        $this->_dataTypeFieldsModel->removeContentTypeField($contentFieldObj->id);
    }
    
    

}
