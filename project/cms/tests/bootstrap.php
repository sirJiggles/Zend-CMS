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
$_SERVER['SERVER_NAME'] = 'jigglycms.com';
define('APPLICATION_ENV', 'production');


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
