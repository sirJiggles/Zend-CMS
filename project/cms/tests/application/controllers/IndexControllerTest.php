<?php

/*
 * This controller tests all of the page functionality that we can test in the system
 * obviously the structure cannot be tested as this is drag and drop and ajax requests
 * etc but we can test the adding / editing / removing and so on for pages
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

// Require the pages model file
require_once '../../api/application/models/Pages.php';

// Require the content model from the API
require_once '../../api/application/models/Content.php';


class IndexControllerTest extends ControllerTestCase
{
    // variable to hold the model instance for templates
    protected $_templateModel = '';
    
    // variable to hold the model instance for pages 
    protected $_pagesModel = '';
    
    // variable to hold the model instance for content
    protected $_contentModel = '';
    
    
    public function setUp(){
        // Set the parent up
        parent::setUp();
       
        // Only get new instances of these if they have not been previously settup
        if ($this->_templateModel == '' 
            && $this->_pagesModel == '' 
            && $this->_contentModel == ''){
            
            // Get an instance of the templates model
            $this->_templateModel = new Application_Model_Templates();

            // Get an instance of the pages model
            $this->_pagesModel = new Application_Model_Pages();

            // Get an instance of the conntent model
            $this->_contentModel = new Application_Model_Content();
        }
       
       
    }
    
    // Test editor has access to index page index action
    public function testEditorAccessIndex(){
        $this->loginEditor();
        $this->dispatch('/');
        $this->assertController('index');
        $this->assertAction('index');
        $this->assertResponseCode(200);
    }
    
    // Test superadmin has access to index page index action
    public function testSuperadminAccessIndex(){
        $this->loginSuperAdmin();
        $this->dispatch('/');
        $this->assertController('index');
        $this->assertAction('index');
        $this->assertResponseCode(200);
    }
    
    /*
     * There is not much more we can test on the index page to be honest as it
     * is mainly generated from structure wich will be a nightmare to test
     */
    
    // Test editor can add a page to the system
    public function testEditorCanAddPage(){
        $this->loginEditor();
        $this->dispatch('/index/add');
        $this->assertController('index');
        $this->assertAction('add');
        $this->assertResponseCode(200);
    }
    
    // Test superadmin can add page to the system
    public function testSuperAdminCanAddPage(){
        $this->loginSuperAdmin();
        $this->dispatch('/index/add');
        $this->assertController('index');
        $this->assertAction('add');
        $this->assertResponseCode(200);
    }
    
    // Test we can add a page to the system
    public function testAddPageCorrect(){

        $templates = $this->_getTemplateArray();
        
        // Set the fake values for the test page
        $pageFields = array('name' => 'testPageOne',
                            'template' => $templates[0]);
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost($pageFields);

        // Now set the dispatch
        $this->dispatch('/index/add');
        
        $this->assertAction('add');
        
        // Assert that we are redirected to the main page
        $this->assertRedirectTo('/');
        
        // Check the database to make sure the page is in there ok
        $contentCheck = $this->_pagesModel->getPageByName('testPageOne');
        
        // check we got something back
        $this->assertNotEquals(null, $contentCheck, 'Could not get the fake page from the database, add correct failed'); 
    }
    
    // Test we can add a second page correctly
    public function testAddSecondPageCorrect(){
        
        $templates = $this->_getTemplateArray();
        
        // Set the fake values for the test page
        $pageFields = array('name' => 'testPageTwo',
                            'template' => $templates[0]);
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost($pageFields);

        // Now set the dispatch
        $this->dispatch('/index/add');
        
        $this->assertAction('add');
        
        // Assert that we are redirected to the main page
        $this->assertRedirectTo('/');
        
        // Check the database to make sure the page is in there ok
        $contentCheck = $this->_pagesModel->getPageByName('testPageTwo');
        
        // check we got something back
        $this->assertNotEquals(null, $contentCheck, 'Could not get the fake page from the database, add second correct failed');
        
    }
    
    // Test the name taken error for adding a page
    public function testNameTakenAddPage(){
        
        $templates = $this->_getTemplateArray();
        
        // Set the fake values for the test page
        $pageFields = array('name' => 'testPageTwo',
                            'template' => $templates[0]);
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost($pageFields);

        // Now set the dispatch
        $this->dispatch('/index/add');
        
        $this->assertAction('add');
        
        $this->assertNotRedirect();
        
        // Check for the error message on the page
        $this->assertQueryContentContains('.ui-state-highlight p', 'That page name is already taken, please try again');
    }
    
    // Test the input required error messages for the add fucntionality
    public function testRequiredInputsAdd(){
        
        // Create fake data missing the name
        $fakeData = array('name' => '', 'template' => '1');
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost($fakeData);
        
        // Now set the dispatch
        $this->dispatch('/index/add');
        
        $this->assertAction('add');
        
        $this->assertNotRedirect();
        
        $this->assertQueryContentContains('ul.errors li', 'Name is required');

    }
    
    // Test the superadmins can edit pages
    public function testSuperAdminCanEditPage(){
        $this->loginSuperAdmin();
        
        // Get the first page
        $page = $this->_pagesModel->getPageByName('testPageOne');
        $this->assertNotEquals(null, $page, 'Could not get the fake page from the database, test superamdin access edit failed');
        
        $this->dispatch('/index/edit/id/'.$page->id);
        $this->assertController('index');
        $this->assertAction('edit');
        $this->assertResponseCode(200);
        
    }
    
    // Test editors can edit pages
    public function testEditorCanEditPage(){
        $this->loginEditor();
        
        // Get the first page
        $page = $this->_pagesModel->getPageByName('testPageOne');
        $this->assertNotEquals(null, $page, 'Could not get the fake page from the database, test editor access edit failed');
        
        $this->dispatch('/index/edit/id/'.$page->id);
        $this->assertController('index');
        $this->assertAction('edit');
        $this->assertResponseCode(200);
        
    }
    
    // Test that we can edit the first page we created correctly
    public function testEditPageOneCorrect(){
        
        // Get the page by page name
        $page = $this->_pagesModel->getPageByName('testPageOne');
        // Check it is correct
        $this->assertNotEquals(null, $page, 'Could not get the fake page from the database, edit page one failed');
        
        // Get the template array
        $templates = $this->_getTemplateArray();
        
        // Create the spoof data for the edit action
        $fakeData = array('name' => 'pageRename', 'template' => $templates[0]);
  
        $this->request->setMethod('POST')
             ->setPost($fakeData);
        
        // Dispatch
        $this->dispatch('/index/edit/id/'.$page->id);
        $this->assertAction('edit');
        
        $this->assertRedirectTo('/');
        
        // Check the page has been edited
        $pageCheck = $this->_pagesModel->getPageByName('pageRename');
        $this->assertNotEquals(null, $pageCheck, 'Could not get the fake page renamed from the database, edit page one failed');
    }
    
    
    // Test the name taken error handling for edit page
    public function testNameTakenEditPageOne(){
        
        // Get the page by page name
        $page = $this->_pagesModel->getPageByName('pageRename');
        // Check it is correct
        $this->assertNotEquals(null, $page, 'Could not get the fake page from the database, test name taken edit failed');
        
        // Get the template array
        $templates = $this->_getTemplateArray();
        
        // Create the spoof data for the edit action
        $fakeData = array('name' => 'testPageTwo', 'template' => $templates[0]);
  
        $this->request->setMethod('POST')
             ->setPost($fakeData);
        
        // Dispatch
        $this->dispatch('/index/edit/id/'.$page->id);
        $this->assertAction('edit');
        
        
        // Check for error message
        $this->assertQueryContentContains('.ui-state-highlight p', 'That page name is already taken, please try again');
        
    }
    
    // Test required inputs for edit page action
    public function testRequiredInputsEditPage(){
        // Get the page by page name
        $page = $this->_pagesModel->getPageByName('pageRename');
        // Check it is correct
        $this->assertNotEquals(null, $page, 'Could not get the fake page from the database, test required inputs edit one failed');
        
        // Get the template array
        $templates = $this->_getTemplateArray();
        
        // Create the spoof data for the edit action
        $fakeData = array('name' => '', 'template' => $templates[0]);
  
        $this->request->setMethod('POST')
             ->setPost($fakeData);
        
        // Dispatch
        $this->dispatch('/index/edit/id/'.$page->id);
        $this->assertAction('edit');
        
        $this->assertNotRedirect();
        
        // Check the errors
        $this->assertQueryContentContains('ul.errors li', 'Name is required');
    }
    
    // Test the possibility of the page ID not being passed to edit template
    public function testIdPassedToEdit(){
        $this->dispatch('/index/edit/id/someIncorrectData');
        $this->assertRedirectTo('/');
    }
    
    // Test the page id passed to edit page action but the id not found
    public function testIdPassedButNotFoundEdit(){
        $this->dispatch('/index/edit/id/999999999999999999');
        $this->assertRedirectTo('/');
    }
    
    
    // Test editor has access to the ability to edit content assignment for a page
    public function testEditorAbleToEditAssignment(){
        $this->loginEditor();
        // Get the page by page name
        $page = $this->_pagesModel->getPageByName('pageRename');
        // Check it is correct
        $this->assertNotEquals(null, $page, 'Could not get the fake page from the database, test editor access content assignment failed');
        
        $this->dispatch('/index/editassignment/page/'.$page->id.'/id/0');
        $this->assertController('index');
        $this->assertAction('editassignment');
        $this->assertResponseCode(200);
    }
    
    // Test that the superadmin has the ability to edit the content assignment on a page
    public function testSuperAdminAbleToEditAssignment(){
        $this->loginSuperAdmin();
        // Get the page by page name
        $page = $this->_pagesModel->getPageByName('pageRename');
        // Check it is correct
        $this->assertNotEquals(null, $page, 'Could not get the fake page from the database, test superadmin access content assignment failed');
        
        $this->dispatch('/index/editassignment/page/'.$page->id.'/id/0');
        $this->assertController('index');
        $this->assertAction('editassignment');
        $this->assertResponseCode(200);
    }
    
    // Test that we can edit the content assignment for a page correctly
    public function testEditAssignmentCorrect(){
        // Get the page by page name
        $page = $this->_pagesModel->getPageByName('pageRename');
        // Check it is correct
        $this->assertNotEquals(null, $page, 'Could not get the fake page from the database, test superadmin access content assignment failed');
        
        // Need to get all the content that is availible for this type from the api
        $contentAssignment = unserialize($page->content_assigned);
        $currentItem = $contentAssignment[0];
        $contentTypeId = $currentItem['type'];
        
        // Now get all content for content type from system
        $content = $this->_contentModel->getContentByType($contentTypeId);
       
        // Get the id of the first bit of availible content and set it so we use that one
        $fakeData = array('assignment' => $content[0]->id);
        
        // Set the post
        $this->request->setMethod('POST')
             ->setPost($fakeData);
        
        $this->dispatch('/index/editassignment/page/'.$page->id.'/id/0');
        $this->assertController('index');
        $this->assertAction('editassignment');
        
        $this->assertRedirectTo('/');
        
        // Check the database to make sure we have saved the content assignment
        $page = $this->_pagesModel->getPageByName('pageRename');
        $contentAssignment = unserialize($page->content_assigned);
        $currentItem = $contentAssignment[0];
        
        $this->assertEquals($content[0]->id, $currentItem['value'], 'Content assigned is different to what it should be: Edit assignment correct failed');
        
    }
    
    // Test passing in a incorrectly formated page to the edit assignment action
    public function testIncorrectFormatArgumentEditAssignment(){
        $this->dispatch('/index/editassignment/page/someIncorrectData/id/0');
        $this->assertRedirectTo('/');
    }
    
    // Test passing correct format but page not found to edit content assignment action
    public function testPageNotFoundEditAssignment(){
        $this->dispatch('/index/editassignment/page/999999999999999999/id/0');
        $this->assertRedirectTo('/');
    }
    
    // Test passing an incorrectly formated id to editassignment
    public function testParseIncorrectFormatSlotEditAssignment(){
        $page = $this->_pagesModel->getPageByName('pageRename');
        $this->dispatch('/index/editassignment/page/'.$page->id.'/id/someIncorrectFormat');
        $this->assertRedirectTo('/');
    }
    
    // Test passing correctly formated slot id but slot not found edit assinment action
    public function testParseSlotNotFoundEditAssignment(){
        $page = $this->_pagesModel->getPageByName('pageRename');
        $this->dispatch('/index/editassignment/page/'.$page->id.'/id/99999999999999999999');
        $this->assertRedirectTo('/');
    }
    
    // Test the remove Editor has access to the remove confirm action
    public function testEditorCanRemoveConfirm(){
        $this->loginEditor();
        // Get the page by page name
        $page = $this->_pagesModel->getPageByName('pageRename');
        // Check it is correct
        $this->assertNotEquals(null, $page, 'Could not get the fake page from the database, test editor access remove confirm failed');
        
        $this->dispatch('/index/remove-confirm/id/'.$page->id);
        $this->assertController('index');
        $this->assertAction('remove-confirm');
        $this->assertResponseCode(200);
    }
    
    // Test the supeamdin user has access to the remove confirm action
    public function testSuperadminCanRemoveConfirm(){
        $this->loginSuperAdmin();
        // Get the page by page name
        $page = $this->_pagesModel->getPageByName('pageRename');
        // Check it is correct
        $this->assertNotEquals(null, $page, 'Could not get the fake page from the database, test superadmin access remove confirm failed');
        
        $this->dispatch('/index/remove-confirm/id/'.$page->id);
        $this->assertController('index');
        $this->assertAction('remove-confirm');
        $this->assertResponseCode(200);
    }
    
    // Test the admin user can reach the remove confirm action
    public function testAdminRemoveConfirm(){
        // Get the page by page name
        $page = $this->_pagesModel->getPageByName('pageRename');
        // Check it is correct
        $this->assertNotEquals(null, $page, 'Could not get the fake page from the database, test admin access remove confirm failed');
        
        $this->dispatch('/index/remove-confirm/id/'.$page->id);
        $this->assertController('index');
        $this->assertAction('remove-confirm');
        $this->assertResponseCode(200);
    }
    
    // Test passing in a incorrectly formated argument to the remove conifrm page
    public function testIncorrectFormatArgumentRemoveConfirm(){
        $this->dispatch('/index/remove-confirm/id/someIncorrectData');
        $this->assertRedirectTo('/');
    }
    
    // Test passing in a number but for a page that cant be found on remove confirm
    public function testPageNotFoundRemoveConfirm(){
        $this->dispatch('/index/remove-confirm/id/999999999999999999');
        $this->assertRedirectTo('/');
    }
    
    // Test actually removing the first page from the system
    public function testRemoveFirstPageCorrect(){
        // Get the page by page name
        $page = $this->_pagesModel->getPageByName('pageRename');
        // Check it is correct
        $this->assertNotEquals(null, $page, 'Could not get the fake page from the database, test remove page one correct failed');
        
        $this->dispatch('/index/remove/id/'.$page->id);
        $this->assertController('index');
        $this->assertAction('remove');
        
        $this->assertRedirectTo('/');
        
        // Attempt to get the page
        $page = $this->_pagesModel->getPageByName('pageRename');
        // Check it is correct
        $this->assertEquals(null, $page, 'Page still exists in the database, remove page one correct failed');
        
    }
    
    // Test sending incorectly formated arguments to the remove action
    public function testIncorrectArgsRemove(){
        $this->dispatch('/index/remove/id/someIncorrectData');
        $this->assertRedirectTo('/');
    }
    
    // Test sending the id for a page that does not exist to the remove action
    public function testIdButNotFoundRemove(){
        $this->dispatch('/index/remove/id/999999999999999999');
        $this->assertRedirectTo('/');
    }
    
    // Test removing the second page from the system
    public function testRemoveSecondPageCorrect(){
        // Get the page by page name
        $page = $this->_pagesModel->getPageByName('testPageTwo');
        // Check it is correct
        $this->assertNotEquals(null, $page, 'Could not get the fake page from the database, test remove page two correct failed');
        
        $this->dispatch('/index/remove/id/'.$page->id);
        $this->assertController('index');
        $this->assertAction('remove');
        
        $this->assertRedirectTo('/');
        
        // Attempt to get the page
        $page = $this->_pagesModel->getPageByName('testPageTwo');
        // Check it is correct
        $this->assertEquals(null, $page, 'Page still exists in the database, remove page two correct failed');
        
    }
   
    
    /*
     * Utils function to get an array of the templates in the system (just the ids)
     * 
     * @return array $templates
     */
    public function _getTemplateArray(){
        
        $templates = $this->_templateModel->getAllTemplates();
        $templateArray = array();
        foreach($templates as $template){
            $templateArray[] = $template->id;
        }
        return $templateArray;
    }
    
    

}
