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
    
    // For most of the tests we need a content type id so we will define it here
    protected $_contentTypeId = '';
    
    // We will need a secon content type to do all the tests so its defined here
    protected $_secondContentTypeId = '';
    
    public function setUp(){
       // Set the parent up
       parent::setUp();
       // Get an instance of the user controller
       $this->_dataTypeFieldsModel = new Application_Model_DataTypeFields();
       
       // Get the first content type in the system so we can run our tests
       $contentTypes = $this->_dataTypeFieldsModel->getAllDataTypeFields();
       
       // Sanity checking
       if ($contentTypes == null){
           exit('we cannot test the content type fields as we have no content types defined!');
       }
       
       // Need two content types at least to do all the correct testing
       if (!isset($contentTypes[1])){
           exit('The system needs at least two content types to be tested!');
       }

       $this->_contentTypeId = $contentTypes[0]->id;
       $this->_secondContentTypeId = $contentTypes[1]->id;

    }
    
    // This est should redirect the user to datatypes
    public function testNoParamsIndexPage()
    {
        // We need to be superadmin to get to this page
        $this->loginSuperAdmin();
        
        $this->dispatch('/datatypefields');
        $this->assertRedirect('/datatypes');
        
    }
    
    // Test the index page
    public function testIndexPage(){
        $this->loginSuperAdmin();
        $this->dispatch('/datatypefields/index/id/'.$this->_contentTypeId);
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
        $this->dispatch('/datatypefields/index/id/'.$this->_contentTypeId);
        $this->assertRedirectTo('/error/not-the-droids');
    }
    
    // Test to make sure admins cannot get to this section
    public function testAdminNoAccess(){
        // Lets first login as an editor
        $this->loginAdmin();
        
        // Now we will attempt to access the content types section
        $this->dispatch('/datatypefields/index/id/'.$this->_contentTypeId);
        $this->assertRedirectTo('/error/not-the-droids');
    }
    
   
    // Test that we can add a content type field to the system
    public function testAddCorrect(){
        
        $this->loginSuperAdmin();
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost(array('name' => 'TestContentTypeField',
                             'format' => 'text',
                             'content_type' => $this->_contentTypeId));
        
        // Make sure we are in the right place and have the right things on screen
        $this->dispatch('/datatypefields/add/content-type/'.$this->contentTypeId);
        $this->assertAction('add');
        
        // Assert that we are redirected to the data type fields manage screen, thus added
        $this->assertRedirectTo('/datatypefields/index/id/'.$this->_contentTypeId);
        
    }
    
    // Test required inputs for content type fields
    public function testRequiredInputs(){
        
        $this->loginSuperAdmin();
        
        $this->request->setMethod('POST')
            ->setPost(array('name' => ''));
        
        // Post the data to the following location
        $this->dispatch('/datatypefields/add/content-type/'.$this->_contentTypeId);
        $this->assertAction('add');
        
        // Test error message
        $this->assertQueryContentContains('ul.errors li', 'Name is required');

    }
    

    /*
     * Now that we have added a content type field we are going to test to make
     * sure we cant add a field to this content type by the same name
     */
    public function testNameTakenAdd(){
        
        $this->loginSuperAdmin();
        
        $this->request->setMethod('POST')
             ->setPost(array('name' => 'TestContentTypeField',
                             'format' => 'text',
                             'content_type' => $this->_contentTypeId));
        
        // Post the data to the following location
        $this->dispatch('/datatypefields/add/content-type/'.$this->_contentTypeId);
        $this->assertAction('add');
        
        $this->assertQueryContentContains('.ui-state-highlight p', 'That name is taken, please try again');
    }
    
    /*
     * Now we are going to add a data field to the second content type for
     * later testing
     */
    public function testAddSecondCorrect(){
        
        $this->loginSuperAdmin();
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost(array('name' => 'TestContentTypeFieldTwo',
                             'format' => 'text',
                             'content_type' => $this->_secondContentTypeId));
        
        // Make sure we are in the right place and have the right things on screen
        $this->dispatch('/datatypefields/add/content-type/'.$this->_secondContentTypeId);
        $this->assertAction('add');
        
        // Assert that we are redirected to the data type fields manage screen, thus added
        $this->assertRedirectTo('/datatypefields/index/id/'.$this->_secondContentTypeId);
        
    }
    
    /*
     * Test that we can edit the first content field in the first content type
     */
    public function testEditFirstContentTypeField(){
        $this->loginSuperAdmin();
        
        // First we need to know the ID of the content type field we want to edit
        $contentFieldObj = $this->_dataTypeFieldsModel->getContentFieldByName('TestContentTypeField', $this->__contentTypeId);
        $contentFieldId = $contentFieldObj->id;
                
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost(array('name' => 'TestContentTypeFieldRename',
                             'format' => 'text',
                             'content_type' => $this->_contentTypeId));
        
        // Make sure we are in the right place and have the right things on screen
        $this->dispatch('/datatypefields/edit/id/'.$contentFieldId.'/content-type/'.$this->_contentTypeId);
        $this->assertAction('edit');
        
        // Assert that we are redirected to the data type fields manage screen, thus edited
        $this->assertRedirectTo('/datatypefields/index/id'.$this->_contentTypeId);
    }
    
    /*
     * Test that when we now try to change the name of the first one to be the
     * name of the second it lets us as content type fields can have the same
     * name if in a different content type
     */
    public function testCanNameFieldSameAsFieldInOtherType(){
        $this->loginSuperAdmin();
        
        // First we need to know the ID of the content type field we want to edit
        $contentFieldObj = $this->_dataTypeFieldsModel->getContentFieldByName('TestContentTypeFieldRename', $this->__contentTypeId);
        $contentFieldId = $contentFieldObj->id;
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost(array('name' => 'TestContentTypeFieldTwo',
                             'format' => 'text',
                             'content_type' => $this->_contentTypeId));
        
        // Make sure we are in the right place and have the right things on screen
        $this->dispatch('/datatypefields/edit/id/'.$contentFieldId.'/content-type/'.$this->_contentTypeId);
        $this->assertAction('edit');
        
        // Assert that we are redirected to the data type fields manage screen, thus edited
        $this->assertRedirectTo('/datatypefields/index/id'.$this->_contentTypeId);
    }
    
    /*
     * Test that we cant add a field to the first content type with the same 
     * name as the one that is currently in there 
     */
    public function testNameTakenOnFieldInContentType(){
        
        $this->request->setMethod('POST')
             ->setPost(array('name' => 'TestContentTypeFieldTwo',
                             'format' => 'text',
                             'content_type' => $this->_contentTypeId));
        
        // Make sure we are in the right place and have the right things on screen
        $this->dispatch('/datatypefields/add/content-type/'.$this->contentTypeId);
        $this->assertAction('add');
        
        // Assert that we are redirected to the data type fields manage screen
        $this->assertRedirectTo('/datatypefields/index/id/'.$this->_contentTypeId);
        
        /*
         * Get the items from this content type to make sure there are not
         * now teo with the same name
         */
        $contentFields = $this->_dataTypeFieldsModel->getDataFieldsForDataType($this->_contentTypeId);
        $duplicateFound = false;
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
        $this->loginSuperAdmin();
        $this->dispatch('/datatypefields/edit/id/sdasd/content-type/'.$this->_contentTypeId);
        $this->assertRedirectTo('/datatypefields/index/id/'.$this->_contentTypeId);
    }
    
    // Test the field is not found on edit action
    public function testContentFieldNotFound(){
        $this->loginSuperAdmin();
        $this->dispatch('/datatypefields/edit/id/9999999999999999999999/content-type/'.$this->_contentTypeId);
        $this->assertRedirectTo('/datatypefields/index/id/'.$this->_contentTypeId);
    }
    
    /*
     * Test that if you go to edit a content field even if you have the right
     * id but don't pass the content-type id you are redirected
     */
    public function testEditCorrectParamButNoContentType(){
        $this->loginSuperAdmin();
        $contentFieldObj = $this->_dataTypeFieldsModel->getContentFieldByName('TestContentTypeFieldTwo', $this->_contentTypeId);
        $contentFieldId = $contentFieldObj->id;
        
        $this->dispatch('/datatypefields/edit/id/'.$contentFieldId);
        $this->assertRedirectTo('/datatypes');
    }
    
    /*
     * Test that we have not sent a content type to the add action and are 
     * redirected to the main manage page
     */
    public function testAddCorrectButNoContentType(){
        $this->loginSuperAdmin();
        $this->dispatch('/datatypefields/add');
        $this->assertRedirectTo('/datatypes');
    }
    
    
    // Test unable to locate the field for removing the content type field
    public function testCantLocateRemoveUserId(){
        $this->loginSuperAdmin();
        
        $this->dispatch('/datatypefields/remove/id/9999999999999999999999/content-type/'.$this->_contentTypeId);
        $this->assertRedirectTo('/datatypes');
    }
    
    // Test correct remove user id but no content-type passed
    public function testCorrectRemoveidNoContentType(){
        $contentFieldObj = $this->_dataTypeFieldsModel->getContentFieldByName('TestContentTypeFieldTwo', $this->_contentTypeId);
        $contentFieldId = $contentFieldObj->id;
        $this->loginSuperAdmin();
        $this->dispatch('/datatypefields/remove/id/'.$contentFieldId);
        $this->assertRedirectTo('datatypes');
    }
    
    
    /*
     * Here we check that we can actually remove the first content type
     */
    public function testRemoveContentTypeFieldOne(){
        $this->loginSuperAdmin();
        $contentFieldObj = $this->_dataTypeFieldsModel->getContentFieldByName('TestContentTypeFieldTwo', $this->_contentTypeId);
        $contentFieldId = $contentFieldObj->id;

        $this->dispatch('/datatypefields/remove/id/'.$contentFieldId.'/content-type/'.$this->_contentTypeId);
        $this->assertRedirectTo('/datatypefields/index/id/'.$this->_contentTypeId);

        // Test the content type field has been removed from the db
        $contentFieldObj = $this->_dataTypeFieldsModel->getContentFieldByName('TestContentTypeFieldTwo', $this->_contentTypeId);
        $this->assertEquals(null, $contentFieldObj, 'Content Type Field has not been removed!');
        
    }
    
    /*
     * Now we clean up all the other test content type fields as we dont need them anymore
     */
    public function testCleanup(){
        $contentFieldObj = $this->_dataTypeFieldsModel->getContentFieldByName('TestContentTypeFieldTwo', $this->_secondContentTypeId);
        $this->_dataTypeFieldsModel->removeContentTypeField($contentFieldObj->id);
    }
    

}
