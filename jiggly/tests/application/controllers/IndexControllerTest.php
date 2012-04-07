<?php

/*
 * This is the controller for testing the index controller in the system that
 * deals with all index-esq functionality.
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Test_Controllers
 */

class IndexControllerTest extends ControllerTestCase
{
    
    public function setUp(){
       // Set the parent up
       parent::setUp();
    }
    
    // Test that the index page exists
    public function testLandOnIndexPage()
    {
        $this->assertTrue(true);
        $this->dispatch('user');
        $this->assertController('user');
        $this->assertAction('index');
        $this->assertResponseCode(200);
    }
    

}
