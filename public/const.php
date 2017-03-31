<?php
// Define code
defined('OK') || define ( 'OK', 200 );
defined('NG') || define ( 'NG', 201 );
defined('EXCPT') || define ( 'EXCPT', 202 );
defined('EMPT') || define ( 'EMPT', 203 );
defined('DELIMITER') || define ( 'DELIMITER', '|#|@' );
defined('SECURITYKEY') || define ( 'SECURITYKEY', 'dental');
defined('LOGSQL') || define ( 'LOGSQL', true);
defined('DEBUG') || define ( 'DEBUG', true);

class user {
	public $user_id;
	public $company_id;
	public $token;
	public $username;
}