<?php
/**
 *
 * @author        GiangNT
 * @package       Base
 *
 */

class Base_AppController extends Zend_Controller_Action {
	protected $db;
	protected $token;
	protected $rsp;

	public function init() {
		$this->db = Zend_Db_Table::getDefaultAdapter();
		$this->rsp = new Respon();
	}

	public function checkSame($a, $b) {
		return strcmp($a, $b)== 0 ? true : false;
	}

	public function sendMail($send_to_email, $subject, $body) {
			//Initialize needed variables
			$your_name = 'SYSTEM';
			$your_email = '@gmail.com'; //Or your_email@gmail.com for Gmail
			$your_password = '$$$';
			$send_to_name = 'member';

			//SMTP server configuration
			$smtpHost = 'smtp.gmail.com';
			$smtpConf = array(
					'auth' => 'login',
					'ssl' => 'tls',
					'port' => '587', // or 587, 25
// 					'ssl' => 'ssl',
// 					'port' => '465',
					'username' => $your_email,
					'password' => $your_password
			);
			$transport = new Zend_Mail_Transport_Smtp($smtpHost, $smtpConf);

			//Create email
			$mail = new Zend_Mail('UTF-8');
			$mail->setBodyText($body);
			$mail->setFrom($your_email, $your_name);
			$mail->addTo($send_to_email, $send_to_name);
			$mail->setSubject($subject);
			return $mail->send($transport);
	}
}
