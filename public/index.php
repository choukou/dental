<?php
date_default_timezone_set("Asia/Ho_Chi_Minh");
require_once 'const.php';

if (! defined ( 'DS' )) {
	define ( 'DS', DIRECTORY_SEPARATOR );
}
if (! defined ( 'ROOT' )) {
	define ( 'ROOT', realpath ( dirname ( __FILE__ ) ) );
}

// Define path to application directory
defined('LIBRARY_PATH')
	|| define('LIBRARY_PATH', realpath(dirname(__FILE__) . '/../library'));
defined('APPLICATION_PATH')
	|| define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

defined('PUBLIC_PATH')
	||  define('PUBLIC_PATH', realpath(dirname(__FILE__)));
// Define path to download directory
defined ( 'DOWNLOAD_PATH' ) || define ( 'DOWNLOAD_PATH', realpath ( dirname ( __FILE__ ) . '/download' ) );

// Define path to upload directory
defined ( 'UPLOAD_PATH' ) || define ( 'UPLOAD_PATH', realpath ( dirname ( __FILE__ ) . '/upload' ) );

// Define application environment
defined('APPLICATION_ENV')
	|| define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'development'));
// Ensure library/ is on include_path
set_include_path(implode(
PATH_SEPARATOR, array(
	realpath(APPLICATION_PATH . '/../library'),
	realpath(APPLICATION_PATH . '/models'),
	get_include_path(),
)));


/** Zend_Application */
require_once 'Zend/Application.php';
// Create application, bootstrap, and run
$application = new Zend_Application(
	APPLICATION_ENV,
	APPLICATION_PATH . '/configs/application.ini'
);

$application->bootstrap()
			->run();

