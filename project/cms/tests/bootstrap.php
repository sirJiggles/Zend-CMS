<?php

/*
 * This is the bootstrap file for our test suite
 * 
 * All code in this project is under the GNU general public licence, full 
 * terms and conditions can be found online: http://www.gnu.org/copyleft/gpl.html
 * 
 * @author Gareth Fuller <gareth-fuller@hotmail.co.uk>
 * @copyright Copyright (c) Gareth Fuller
 * @package Test_Bootstraps
 */

// Set error reporting
error_reporting( E_ALL | E_STRICT );

// Define path to application directory

if (!defined('APPLICATION_PATH')){
    define('APPLICATION_PATH', realpath(dirname(__FILE__)) . '/../application');
}

// Custom variables 
define('TESTS_PATH', realpath(dirname(__FILE__)));

// Spoof the server name for session data
//$_SERVER['SERVER_NAME'] = 'jigglycms.com';
$_SERVER['SERVER_NAME'] = 'jiggly.dev';

// Spoof the user agent for the mobile detection
$_SERVER['HTTP_USER_AGENT'] = 'Mozilla/5.0 (X11; Linux i686) AppleWebKit/535.19 (KHTML, like Gecko) Ubuntu/11.10 Chromium/18.0.1025.142 Chrome/18.0.1025.142 Safari/535.19';

// Set the testing environment URL
//define('APPLICATION_ENV', 'production');
define('APPLICATION_ENV', 'development');


// Ensure library/ is on include_path (we will just add all possible zf ones for now as deving on multiple machines)
set_include_path(implode(PATH_SEPARATOR, array(
    realpath('/library/Zend/1.11.11'),
    realpath('/Subversion/libary/Zend/1.11.11'),
    realpath('/home/gareth/Dropbox/library/Zend/1.11.11'),
    get_include_path(),
)));

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

require_once 'Zend/Application.php';

// This class contains the set up tear down and init of ZF
require_once 'ControllerTestCase.php';
