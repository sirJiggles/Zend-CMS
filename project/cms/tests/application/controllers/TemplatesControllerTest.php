<?php

/*
 * This is the controller for testing all the template operations
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Test_Controllers
 */

// Require the templates model file
require_once '../../api/application/models/Templates.php';

// Require the model file for content types
require_once '../../api/application/models/ContentTypes.php';


class TemplatesControllerTest extends ControllerTestCase
{
    
    // We need the model from the api for util functions like get test templates
    protected $_templateModel = '';
    
    // Need the content types to simulate adding different ammount of content
    // types and so on into the system
    protected $_contentTypes = '';
    
    
    public function setUp(){
       // Set the parent up (disable the admin login)
       parent::setUp();
       // Get an instance of the templates model
       $this->_templateModel = new Application_Model_Templates();
       
       // Get an instance of the content types model
       $this->_contentTypes = new Application_Model_ContentTypes();
       
       // As we need tp be super admin for these tests log in as one
       $this->loginSuperAdmin();
       
    }
    
    // Test the index page
    public function testIndexPage(){
       
        $this->dispatch('/templates');
        $this->assertNotRedirect();
        $this->assertController('templates');
        $this->assertAction('index');
        $this->assertResponseCode(200); 
    }
    
    // Test that a system editor can access this page
    public function testEditorHasNoAccessIndex(){
        $this->loginEditor();
        $this->dispatch('/templates/');
        $this->assertRedirectTo('/error/not-the-droids'); 
    }
    
    // Test editor has no access to add page
    public function testEditorHasNoAccessAdd(){
        $this->loginEditor();
        $this->dispatch('/templates/add');
        $this->assertRedirectTo('/error/not-the-droids'); 
    }
    
    // Test admin has no access to index
    public function testAdminNoAccessIndex(){
        $this->loginAdmin();
        $this->dispatch('/templates/');
        $this->assertRedirectTo('/error/not-the-droids'); 
    }
    
    // Test admin has no access to add page
    public function testAdminNoAccessAdd(){
        $this->loginAdmin();
        $this->dispatch('/templates/add');
        $this->assertRedirectTo('/error/not-the-droids'); 
    }
    
    // testsuperadmin has access to add
    public function testSuperAdminAccessAdd(){
        $this->dispatch('/templates/add');
        $this->assertNotRedirect();
        $this->assertController('templates');
        $this->assertAction('add');
        $this->assertResponseCode(200);
        
        
    }
    
    
    // Test we can add templates to the system correctly
    public function testAddTemplateCorrect(){

        // Set the name and file for the template we are about to create
        $templateFields = array('name' => 'testTemplateOnePhpUnit',
                                'file' => 'test.php');
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost($templateFields);

        // Now set the dispatch
        $this->dispatch('/templates/add');
        
        $this->assertAction('add');
        
        // Assert that we are redirected to the templates manage screen
        $this->assertRedirectTo('/templates');
        
        // Check the database to make sure the template is in there ok
        $contentCheck = $this->_templateModel->getTemplateByName('testTemplateOnePhpUnit');
        
        // check we got something back
        $this->assertNotEquals(null, $contentCheck, 'Could not get the fake template from the database, add correct failed');
         
    }
    
    // Test that we can add a second template into the system
    public function testAddSecondTemplateCorrect(){
        // Set the name and file for the template we are about to create
        $templateFields = array('name' => 'testTemplateTwoPhpUnit',
                                'file' => 'test.php');
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost($templateFields);

        // Now set the dispatch
        $this->dispatch('/templates/add');
        
        $this->assertAction('add');
        
        // Assert that we are redirected to the templates manage screen
        $this->assertRedirectTo('/templates');
        
        // Check the database to make sure the template is in there ok
        $contentCheck = $this->_templateModel->getTemplateByName('testTemplateTwoPhpUnit');
        
        // check we got something back
        $this->assertNotEquals(null, $contentCheck, 'Could not get the fake template two from the database, add correct failed');
         
    }
    
    // Test name taken error message for the add template action
    public function testNameTakenAdd(){
        
        // Create the fake data with the same name as the one we just setup
        $templateFields = array('name' => 'testTemplateTwoPhpUnit',
                                'file' => 'test.php');
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost($templateFields);
        
        // Now set the dispatch
        $this->dispatch('/templates/add');
        
        $this->assertAction('add');
        
        // Check for the error message on the page
        $this->assertQueryContentContains('.ui-state-highlight p', 'That name is already taken, please try again');
    }
    
    // Test the input required error messages for the add fucntionality
    public function testRequiredInputsAdd(){
        // Create fake data missing the name
        $fakeData = array('name' => '', 'file' => 'test.php');
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost($fakeData);
        
        // Now set the dispatch
        $this->dispatch('/templates/add');
        
        $this->assertAction('add');
        
        $this->assertQueryContentContains('ul.errors li', 'Name is required');

    }
    
    // Test that we can edit the first template (for this we will assign the first
    // availible content type to the template)
    public function testEditFirstTemplateCorrect(){
        
        // Get the current template so we can get the id of the one we are editing
        $template = $this->_templateModel->getTemplateByName('testTemplateOnePhpUnit');
        
        // Get the content types 
        $contentTypes = $this->_contentTypes->getAllContentTypes();
        
        $fakeData = array();
        
        // Add 2 of the first content type to the template
        $type = $contentTypes[1]->id;
        $fakeData['content_'.$type] = 2;
        
        // Also change the name of the template
        $fakeData['name'] = 'testTemplateOnePhpUnitRename';
        $fakeData['file'] = 'test.php';
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost($fakeData);
        
        // Now set the dispatch
        $this->dispatch('/templates/edit/id/'.$template->id);
        
        $this->assertAction('edit');
        
        // Get the template from the system to make sure it has been changed
        $template = $this->_templateModel->getTemplateByName('testTemplateOnePhpUnitRename');
        
        $this->assertNotEquals(null, $template, 'Could not get the fake template one from the database, edit correct failed');
         
    }
    
    // Test the name take fucntionality for edit template
    public function testNameTakenEdit(){
        // Get the current template so we can get the id of the one we are editing
        $template = $this->_templateModel->getTemplateByName('testTemplateOnePhpUnitRename');
        
        // Get the content types 
        $contentTypes = $this->_contentTypes->getAllContentTypes();
        
        $fakeData = array();
        
        // Add 2 of the first content type to the template
        $type = $contentTypes[1]->id;
        $fakeData['content_'.$type] = 2;
        // Also change the name of the template
        $fakeData['name'] = 'testTemplateTwoPhpUnit';
        $fakeData['file'] = 'test.php';
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost($fakeData);
        
        // Now set the dispatch
        $this->dispatch('/templates/edit/id/'.$template->id);
        
        $this->assertAction('edit');
        
        $this->assertQueryContentContains('.ui-state-highlight p', 'That name is already taken, please try again');
    }
    
    // Test the possibility of the template ID not being passed to edit template
    public function testIdPassedToEdit(){
        $this->dispatch('/templates/edit/id/someIncorrectData');
        $this->assertRedirectTo('/templates');
    }
    
    // Test the template id passed to edit template action but the id not found
    public function testIdPassedButNotFoundEdit(){
        $this->dispatch('/templates/edit/id/999999999999999999');
        $this->assertRedirectTo('/templates');
    }
    
    // Test accessing the remove confirm action correctly
    public function testRemoveConfirmCorrect(){
        // Get the first template from the system to get its ID
        $template = $this->_templateModel->getTemplateByName('testTemplateOnePhpUnitRename');
        
        // Hit the page
        $this->dispatch('/templates/remove-confirm/id/'.$template->id);
        $this->assertAction('remove-confirm');
        $this->assertController('templates');
        $this->assertNotRedirect();
        $this->assertResponseCode(200);
    }
    
    // Test passing in a incorrectly formated argument to the remove conifrm page
    public function testIncorrectFormatArgumentRemoveConfirm(){
        $this->dispatch('/templates/remove-confirm/id/someIncorrectData');
        $this->assertRedirectTo('/templates');
    }
    
    // Test passing in a number but for a template that cant be found on remove confirm
    public function testTemplateNotFoundRemoveConfirm(){
        $this->dispatch('/templates/remove-confirm/id/999999999999999999');
        $this->assertRedirectTo('/templates');
    }
   
    // Test actually removing the first template from the system
    public function testRemoveTemplateCorrect(){
        $template = $this->_templateModel->getTemplateByName('testTemplateOnePhpUnitRename');
        
        // Hit the page
        $this->dispatch('/templates/remove/id/'.$template->id);
        $this->assertRedirectTo('/templates');
        
        // Check the content has been removed
        $template = $this->_templateModel->getTemplateByName('testTemplateOnePhpUnitRename');
        $this->assertEquals(null, $template, 'Template still exists in system should have been removed');
         
    }
    
    // Test sending incorectly formated arguments to the remove action
    public function testIncorrectArgsRemove(){
        $this->dispatch('/templates/remove/id/someIncorrectData');
        $this->assertRedirectTo('/templates');
    }
    
    // Test sending the id for a template that does not exist to the remove action
    public function testIdButNotFoundRemove(){
        $this->dispatch('/templates/remove/id/999999999999999999');
        $this->assertRedirectTo('/templates');
    }
    
    // Lastly remove our second test template from the system
    public function testRemoveSecondTemplate(){
        $template = $this->_templateModel->getTemplateByName('testTemplateTwoPhpUnit');
        
        // Hit the page
        $this->dispatch('/templates/remove/id/'.$template->id);
        $this->assertRedirectTo('/templates');
        
        // Check the content has been removed
        $template = $this->_templateModel->getTemplateByName('testTemplateTwoPhpUnit');
        $this->assertEquals(null, $template, 'Template still exists in system should have been removed');
    }
    

}
