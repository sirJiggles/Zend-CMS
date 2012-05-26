<?php

/*
 * This is the controller for testing all the content operations
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Test_Controllers
 */

// Get an instance the content model file
require_once '../../api/application/models/Content.php';

// Get instance of the model for getting the content type fields
require_once '../../api/application/models/ContentTypeFields.php';

class ContentControllerTest extends ControllerTestCase
{
    
    // We need the model from the api for util functions like get test content types
    protected $_contentModel = '';
    
    // Need an instance of the contet type fields so that we can check the filds
    // types of the dummy content we are going to add / remove
    protected $_contentTypeFields = '';
    
    public function setUp(){
       // Set the parent up (disable the admin login)
       parent::setUp();
       // Get an instance of the content model
       $this->_contentModel = new Application_Model_Content();
       
       // Get an instance of the content type fields model
       $this->_contentTypeFields = new Application_Model_ContentTypeFields();
       
    }
    
    // Test the index page
    public function testIndexPage(){
       
        $this->dispatch('/content');
        $this->assertNotRedirect();
        $this->assertController('content');
        $this->assertAction('index');
        $this->assertResponseCode(200); 
    }
    
    // Test that a system editor can access this page
    public function testEditorHasAccessIndex(){
        // Login as editor
        $this->loginEditor();
        
        // Go to index
        $this->dispatch('/content');
        $this->assertNotRedirect();
        $this->assertController('content');
        $this->assertAction('index');
        $this->assertResponseCode(200); 
    }
    
    // Test Editor has access to the add funcitonality
    public function testEditorAccessAdd(){
        // Login as editor
        $this->loginEditor();
       
        // Go to ad form fer the first content type
        $this->dispatch('/content/add/id/1');
        $this->assertNotRedirect();
        $this->assertController('content');
        $this->assertAction('add');
        $this->assertResponseCode(200);
    }
    
    // Test that the admin user has access to the add page as we have 
    // already checked the index page as an admin
    public function testAdminAccessAddAction(){
        
        // Go to ad form fer the first content type
        $this->dispatch('/content/add/id/1');
        $this->assertNotRedirect();
        $this->assertController('content');
        $this->assertAction('add');
        $this->assertResponseCode(200);
    }
   
    // As an admin we are now going to add some dummy content
    public function testAddContentCorrect(){
        
        // First we need to work out what content tye fields we have
        // for the content type so we can actually populate the form
        // so get the fields for the first type from the API
        $contentFields = $this->_contentTypeFields->getContentFieldsForContentType(1);
        
        // check we got something back
        $this->assertNotEquals(null, $contentFields, 'Could not get contet fields for type 1 (does type 1 exist?)');
        
        // create the form array here
        $fakeData = $this->_spoofFormatContent($contentFields);
        
        // Finish the fake data with where it goes and the ref!
        $fakeData['content_type'] = '1';
        $fakeData['ref'] = 'unitTestingContentOne';
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost($fakeData);

        // Now set the dispatch
        $this->dispatch('/content/add/id/1');
        
        $this->assertAction('add');
        
        // Assert that we are redirected to the data type fields manage screen
        $this->assertRedirectTo('/content');
        
        // Check the database to make sure the content is in there ok
        $contentCheck = $this->_contentModel->getByRef('unitTestingContentOne');
        
        // check we got something back
        $this->assertNotEquals(null, $contentCheck, 'Could not get the fake content from the database, add correct failed');
        
        
        
    }
    
    // As an admin we are now going to add some dummy content mark II
    public function testAddContentCorrectSecond(){
        

       // First we need to work out what content tye fields we have
        // for the content type so we can actually populate the form
        // so get the fields for the first type from the API
        $contentFields = $this->_contentTypeFields->getContentFieldsForContentType(1);
        
        // check we got something back
        $this->assertNotEquals(null, $contentFields, 'Could not get contet fields for type 1 (does type 1 exist?)');
        
        // create the form array here
        $fakeData = $this->_spoofFormatContent($contentFields);
        
        // Finish the fake data with where it goes and the ref!
        $fakeData['content_type'] = '1';
        $fakeData['ref'] = 'unitTestingContentDuplicate';
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost($fakeData);

        // Now set the dispatch
        $this->dispatch('/content/add/id/1');
        
        $this->assertAction('add');
        
        // Assert that we are redirected to the data type fields manage screen
        $this->assertRedirectTo('/content');
        
        // Check the database to make sure the content is in there ok
        $contentCheck = $this->_contentModel->getByRef('unitTestingContentDuplicate');
        
        // check we got something back
        $this->assertNotEquals(null, $contentCheck, 'Could not get the fake content from the database, add correct failed');
        
        
        
    }
    
    // Test the ref taken functionality when adding content to the system
    public function testRefTakenAdd(){
        
        // First we need to work out what content tye fields we have
        // for the content type so we can actually populate the form
        // so get the fields for the first type from the API
        $contentFields = $this->_contentTypeFields->getContentFieldsForContentType(1);
        
        // check we got something back
        $this->assertNotEquals(null, $contentFields, 'Could not get contet fields for type 1 (does type 1 exist?)');
        
        // create the form array here
        $fakeData = $this->_spoofFormatContent($contentFields);
         
        // Finish the fake data with where it goes and the ref!
        $fakeData['content_type'] = '1';
        // This ref should be taken at this stage!
        $fakeData['ref'] = 'unitTestingContentOne';
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost($fakeData);
        
        // Now set the dispatch
        $this->dispatch('/content/add/id/1');
        
        $this->assertAction('add');
        
        // Check for the error message on the page
        $this->assertQueryContentContains('.ui-state-highlight p', 'That ref is already taken, please try again');
        
    }
    
    // Test the add action with incorrect argument sent to it
    public function testAddActionIncorrectArgsText(){
        
        $this->dispatch('/content/add/id/dfdsfdfdf');
        $this->assertRedirect('/content');
        
    }
    
    // Test the add action with an id passed to it that does not exist (hpopefully)
    public function testAddActionNotFoundArg(){
        $this->dispatch('/content/add/id/999999999999999909');
        $this->assertRedirect('/content');
    }
    
    // Test the edit functionality corectly working
    public function testEditContentCorrect(){
        
        // Get the current content from the database based on its ref
        $currentContent = $this->_contentModel->getByRef('unitTestingContentOne');
        
        // check we got something back
        $this->assertNotEquals(null, $currentContent, 'Could not get the current fake content from the database');
        
        // Rather than formatting the serialised content and so on we will
        // create the edit content in the same way we did for add
        $contentFields = $this->_contentTypeFields->getContentFieldsForContentType(1);
        
        $fakeData = $this->_spoofFormatContent($contentFields, 'edit');
         
        // Finish the fake data with where it goes and the ref!
        $fakeData['content_type'] = '1';
        // we are renaming the ref also!
        $fakeData['ref'] = 'unitTestingContentTwo';
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost($fakeData);
        
        $this->dispatch('/content/edit/id/'.$currentContent->id);
        
        $this->assertAction('edit');
        
        // Taken back to the homepage
        $this->assertRedirect('/');
        
        // Check to make sure the content has beed edited by tyring to get it 
        // by the new ref value
        $currentContent = $this->_contentModel->getByRef('unitTestingContentTwo');
        
        // check we got something back
        $this->assertNotEquals(null, $currentContent, 'Could not get the fake content from the database, edit correct failed');

        
    }
    
    // Test the ref not taken on the current item we are editing (re sbmitting
    // the same ref for edit content
    public function testEditRefTheSameShouldWork(){
        
        // Get the current content from the database based on its ref
        $currentContent = $this->_contentModel->getByRef('unitTestingContentTwo');
        
        // check we got something back
        $this->assertNotEquals(null, $currentContent, 'Could not get the current fake content from the database');
        
        // Rather than formatting the serialised content and so on we will
        // create the edit content in the same way we did for add
        $contentFields = $this->_contentTypeFields->getContentFieldsForContentType(1);
        
        $fakeData = $this->_spoofFormatContent($contentFields, 'edit');
         
        // Finish the fake data with where it goes and the ref!
        $fakeData['content_type'] = '1';
        // same name as it currently is!
        $fakeData['ref'] = 'unitTestingContentTwo';
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost($fakeData);
        
        $this->dispatch('/content/edit/id/'.$currentContent->id);
        
        $this->assertAction('edit');
        
        // Taken back to the homepage, must have worked
        $this->assertRedirect('/');
        
       
    }
    
    // Test that when we edit the content the ref is not taken
    // functionality
    public function testEditContentRefTaken(){
        
        // Get the current content from the database based on its ref
        $currentContent = $this->_contentModel->getByRef('unitTestingContentTwo');
        
        // check we got something back
        $this->assertNotEquals(null, $currentContent, 'Could not get the current fake content from the database');
        
        // Rather than formatting the serialised content and so on we will
        // create the edit content in the same way we did for add
        $contentFields = $this->_contentTypeFields->getContentFieldsForContentType(1);
        
        $fakeData = $this->_spoofFormatContent($contentFields, 'edit');
         
        // Finish the fake data with where it goes and the ref!
        $fakeData['content_type'] = '1';
        // same as our duplicate one
        $fakeData['ref'] = 'unitTestingContentDuplicate';
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost($fakeData);
        
        $this->dispatch('/content/edit/id/'.$currentContent->id);

        $this->assertAction('edit');
        
        // Check for the error message on the page
        $this->assertQueryContentContains('.ui-state-highlight p', 'That ref is already taken, please try again');
        
    }
    
    // Test the edit content action with incorrect format of args
    public function testEditContentIcorrectArgs(){
        $this->dispatch('/content/edit/id/sdsddsf');
        $this->assertRedirect('/');
    }
    
    // Test the edit content action with the not found args
    public function testEditContentNotFoundArgs(){
        $this->dispatch('/content/edit/id/908030934545');
        $this->assertRedirect('/');
        
    }
    
    // Test unable to locate the field for removing the content
    public function testRemoveContentIncorrectId(){
        
        $this->dispatch('/content/remove/id/9999999999999999999999');
        $this->assertRedirectTo('/');
    }
    
    // Test incorrect format of arguments to remove action
    public function testRemoveContentInocrrectFormatArgs(){
        
        $this->dispatch('/content/remove/id/fsdfdsfsdf');
        $this->assertRedirectTo('/');
    }
    
    // Test inocrrect not found id for remove-confirm action
    public function testRemoveConfirmNoIdFound(){
        
        $this->dispatch('/content/remove-confirm/id/983493498378923492345');
        $this->assertRedirectTo('/');
    }
    
    // Test incorrect format of the args for the remove confirm action
    public function testRemoveConfirmIncorrectFormatId(){
        
        $this->dispatch('/content/remove-confirm/id/fsdfdsfsdf');
        $this->assertRedirectTo('/');
    }
    
    // Now test that we can navigate correctly to the remove confirm action
    public function testRemoveConfirmContentCorrect(){
        
        // Get the current content from the database based on its ref
        $currentContent = $this->_contentModel->getByRef('unitTestingContentTwo');
        
        // check we got something back
        $this->assertNotEquals(null, $currentContent, 'Could not get the current fake content from the database');
        
        // Dispatch the remove confirm url
        $this->dispatch('/content/remove-confirm/id/'.$currentContent->id);
        
        $this->assertController('content');
        $this->assertNotRedirect();
        $this->assertAction('remove-confirm');
        $this->assertResponseCode(200); 
        
    }
   
    // Lastly we are going to check that we can remove the content from the
    // syetem and thus clean up our mess!
    public function testRemoveContentForReal(){
         // Get the current content from the database based on its ref
        $currentContent = $this->_contentModel->getByRef('unitTestingContentTwo');
        
        // check we got something back
        $this->assertNotEquals(null, $currentContent, 'Could not get the current fake content from the database');
        
        // Dispatch the remove confirm url
        $this->dispatch('/content/remove/id/'.$currentContent->id);
        
        $this->assertRedirect('/');
        
        // Try to get the content from the system again just to make sure it 
        // is gone
        $currentContent = $this->_contentModel->getByRef('unitTestingContentTwo');
        $this->assertEquals(null, $currentContent, 'The content has not been removed from the system for some reason!');
    }
    
    // Cleanup by removing the duplicate content test
    public function testRemoveDuplicateContentCorrect(){
        
        // Get the current content from the database based on its ref
        $currentContent = $this->_contentModel->getByRef('unitTestingContentDuplicate');
        
        // check we got something back
        $this->assertNotEquals(null, $currentContent, 'Could not get the current fake content from the database');
        
        // Dispatch the remove confirm url
        $this->dispatch('/content/remove/id/'.$currentContent->id);
        
        $this->assertRedirect('/');
        
        // Try to get the content from the system again just to make sure it 
        // is gone
        $currentContent = $this->_contentModel->getByRef('unitTestingContentDuplicate');
        $this->assertEquals(null, $currentContent, 'The content has not been removed from the system for some reason!');
    }
    
    
    
    /*
     * This is a utils function for the add/edit functionality that 
     * will send back some dummy form content based on the input 
     * fields
     * 
     * @param object Zend_Db_Table_Row
     * @param string mode
     * @return array $formdata
     */
    public function _spoofFormatContent($fieldsFromApi, $mode = 'add'){
        
        $fakeData = array();
        
        // build the dummy form data based on the fields (need to change this later)
        foreach($fieldsFromApi as $field){
            switch ($field->format){
                case 'image':
                    if ($mode == 'add'){
                        $fakeData[$field->name] = 'This is some dummy image content';
                    }else{
                        $fakeData[$field->name] = 'This is some dummy image content edit';
                    }
                    break;
                case 'text':
                    if ($mode == 'add'){
                        $fakeData[$field->name] = 'This is some dummy text content';
                    }else{
                        $fakeData[$field->name] = 'This is some dummy text content edit';
                    }
                    break;
                case 'wysiwyg':
                    if ($mode == 'add'){
                        $fakeData[$field->name] = 'This is some dummy wysiwyg content';
                    }else{
                        $fakeData[$field->name] = 'This is some dummy wysiwyg content edit';
                    }
                    break;
                default:
                    break;
            }
        }
        
        return $fakeData;
        
        
    }
    

}
