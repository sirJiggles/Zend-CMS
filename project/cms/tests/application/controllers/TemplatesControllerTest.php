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
    /*public function testSuperAdminAccessAdd(){
        $this->loginSuperAdmin();
        $this->dispatch('/templates/add');
        $this->assertNotRedirect();
        $this->assertController('templates');
        $this->assertAction('add');
        $this->assertResponseCode(200);
    }
    
    // Test we can add templates to the system correctly
    public function testAddTemplateCorrect(){
        // For this we need to spoof our form data so we will
        // get this from one of the internal util functions for this class
        $templateFields = $this->_getSpoofData();
        
        // Set the name of the first content type to be something memorable
        $templateFields['name']=  'testAddTemplateOnePhpUnit';
        
        // Spoof the new details
        $this->request->setMethod('POST')
             ->setPost($templateFields);

        // Now set the dispatch
        $this->dispatch('/templates/add');
        
        $this->assertAction('add');
        
        // Assert that we are redirected to the templates manage screen
        $this->assertRedirectTo('/templates');
        
        // Check the database to make sure the template is in there ok
        $contentCheck = $this->_templateModel->getTemplateByName('testAddTemplateOnePhpUnit');
        
        // check we got something back
        $this->assertNotEquals(null, $contentCheck, 'Could not get the fake template from the database, add correct failed');
        
        
    }
   */
   
    
    /*
     * This is our utils function for getting spoof form data
     * 
     * @return array formData
     */
    public function _getSpoofData(){
        
        // Our return data array
        $fakeData = array();
        
        // First we need to get the content types that are in the system
        $contentTypes = $this->_contentTypes->getAllContentTypes();
        $i = 0;
        foreach($contentTypes as $type){
            // Construct the form data in the format it expects
            $fakeData['content_'.$type->id] = $i;
            $i ++;
        }
        $fakeData['file'] = 'test.php';
        $fakeData['name'] = 'some name';
    }
    

}
