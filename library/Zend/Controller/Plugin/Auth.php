<?php
/**
+====================================
| Project: Logikintai
+====================================
| > Date started: 2014/08/21
| > Author: GiangNT
| > Version : 1.1.0
| > Modified Date : 2015/03/24
+====================================
 */
class Zend_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract{
	private $allow = array(
		'auth',
		'home',
		'error',
	);

	public function int() {
	}

	public function preDispatch(Zend_Controller_Request_Abstract $request){
		try {

			$error = false;
			if (!Zend_Auth::getInstance()->hasIdentity()) {
				$error = true;
			}else{
				$user = Zend_Auth::getInstance()->getIdentity();
				if(!isset($user['user_name'])){
					$error = true;
				}
				$layout = Zend_Layout::getMvcInstance();
				$view = $layout->getView();
				$view->user       = $user;
				$view->request    = $request;
			}
			if( in_array(strtolower($request->getControllerName()), $this->allow)) {
				$error = false;
			}
			if($error){
				if ($request->isXmlHttpRequest()) {
					exit("<script>window.location.href = '/login';</script>");
				} else {
					$request->setModuleName('master')->setControllerName('auth')->setActionName('index')->setParam('backurl', $_SERVER['REQUEST_URI']);
				}
			} else {
				// Regenerate session id, keep data
// 					session_regenerate_id();
			}
		} catch (Exception $e) {
			$this->getResponse()->setHttpResponseCode(403);
		}
	}

	public function preAuth(Zend_Controller_Request_Abstract $request){

	}

}
