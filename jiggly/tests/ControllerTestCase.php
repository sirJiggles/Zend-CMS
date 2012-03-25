<?php

/*
 * This is not so much a controller but more a bootstrap for all of our
 * model and controller tests, all test classes extend this class 
 * and inherit the default setup, tear down and login functions
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Test_Bootstraps
 */

class ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{
    /*
     * @var Zend application object
     */
    protected $application;
    
    /*
     * This function is run first, it is used to set up the test environment
     * here we bootstrap the application, call the parent set up function
     * and then login as an admin (this is done by default unless unset by
     * a test later on)
     */
    public function setUp()
    {
        // Define the setup method of appBootstrap and this
        $this->bootstrap = array($this, 'appBootstrap');
       
        // Set up the parent
        parent::setUp(); 
        
        //Create a fake login
        $this->loginAdmin();
    }
    
    /*
     * This function is called from the set up function, in here we create 
     * a fake zend application and bootstrap it (using the creds form 
     * the application we are testing)
     */
    public function appBootstrap()
    {
        //exit('the value of application env is '.APPLICATION_ENV);
        // Create a new Zend application object
        $this->application = new Zend_Application(
                                    APPLICATION_ENV, 
                                    APPLICATION_PATH .'/configs/application.ini');
        
        $this->application->bootstrap();
    }
    
    /*
     * This is a utils function for cleaning things up, in here we unset some
     * things that may have been setup in previous tests
     */
    public function tearDown()
    {
        Zend_Controller_Front::getInstance()->resetInstance();
        $this->resetRequest();
        $this->resetResponse();

        $this->request->setPost(array());
        $this->request->setQuery(array());
    }
    
    /*
     * This function by default is called from the set up function, in here 
     * we fake some creds in the zend auth storage (admin user) so we can
     * actually dispatch our tests later on.
     */
    public function loginAdmin ()
    {
        // Remove any previous fake accounts
        Zend_Auth::getInstance()->getStorage()->clear();
        
        // create a fake identity
        $identity = new stdClass();
        $identity->role = 'admin';
        $identity->username = 'gfuller';
        $identity->first_name = 'Gareth';
        $identity->last_name = 'Fuller';
        $identity->active = 1;
        
        // Push our fake identity into the auth storage
        Zend_Auth::getInstance()->getStorage()->write($identity);
        
        // Run a test to make sure we have aithentification
        $auth = Zend_Auth::getInstance();
        $this->assertTrue($auth->hasIdentity());
    }
    
    /*
     * This function is very much the same as the login admin function
     * but in this function the fake user that we create is an editor as 
     * appose to an admin user 
     */
    public function loginEditor ()
    {
        // Remove any previous fake accounts
        Zend_Auth::getInstance()->getStorage()->clear();
        
        // create a fake identity
        $identity = new stdClass();
        $identity->role = 'editor';
        $identity->username = 'gfuller';
        $identity->first_name = 'Gareth';
        $identity->last_name = 'Fuller';
        $identity->active = 1;
        
        // Push our fake identity into the auth storage
        Zend_Auth::getInstance()->getStorage()->write($identity);
        
        // Run a test to make sure we have aithentification
        $auth = Zend_Auth::getInstance();
        $this->assertTrue($auth->hasIdentity());
    }
}
