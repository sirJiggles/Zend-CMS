<?php

// Set error reporting
error_reporting( E_ALL | E_STRICT );

// Define path to application directory

if (!defined('APPLICATION_PATH')){
    define('APPLICATION_PATH', realpath(dirname(__FILE__)) . '/../application');
}
// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'testing'));

define('TESTS_PATH', realpath(dirname(__FILE__)));

// Ensure library/ is on include_path (we will just add all possible zf ones for now as deving on multiple machines)
set_include_path(implode(PATH_SEPARATOR, array(
    realpath('/var/www/library/Zend/1.11.11'),
    realpath('/Subversion/libary/Zend/1.11.11'),
    realpath('/home/gareth/Dropbox/library/Zend/1.11.11'),
    realpath(APPLICATION_PATH . '../library'),
    get_include_path(),
)));

require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

require_once 'Zend/Application.php';

// This class contains the set up tear down and init of ZF
require_once 'ControllerTestCase.php';
