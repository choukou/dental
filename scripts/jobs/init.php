<?php
// Define path to application directory
defined('APPLICATION_PATH')
|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../../application'));

// Define path to Public directory
defined('PUBLIC_PATH')
|| define('PUBLIC_PATH', realpath(dirname(__FILE__) . '/../../public'));

defined('LIBRARY_PATH')
|| define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/../../library'));

// Define application environment
defined('APPLICATION_ENV')
|| define('APPLICATION_ENV', 'production');

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
		realpath(APPLICATION_PATH . '/../library'),
		get_include_path(),
)));

require_once PUBLIC_PATH . '/const.php';
require_once LIBRARY_PATH .'/Blockchain/vendor/autoload.php';
/** Zend_Application */
require_once 'Zend/Application.php';
// require_once '/../../public/const.php';
// Create application, bootstrap, and run
$application = new Zend_Application(
		APPLICATION_ENV,
		APPLICATION_PATH . '/configs/application.ini'
		);
$application->bootstrap();


