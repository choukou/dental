<?php
class Auth_LoginController extends Auth_AppController {
	public function init() {
		parent::init();
	}

	public function indexAction(){
		$this->view->title = 'Login';
		$this->_helper->layout()->disableLayout();
		if ($this->auth->hasIdentity()) {
			$this->auth->clearIdentity();
		}
	}

	public function checkAction(){
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$params = $this->getAllParams(true);
		$params['staticSalt'] = SECURITYKEY;
		$params['ip'] = $_SERVER['REMOTE_ADDR'];

		if($this->getRequest()->isPost()){
			try {
				if ($this->auth->hasIdentity()) {
					$this->auth->clearIdentity();
				}
				$rsp = $this->db->callProcedurePrepare('auth_login_check', $params);
				if(isset($rsp[0][0]['user_id'])){
					$storage = $this->auth->getStorage();
					$storage->write($rsp[0][0]);

					$this->respon['auth']['HTTP_REFERER'] = $_SERVER['HTTP_REFERER'];
					$url  = parse_url($_SERVER['HTTP_REFERER']);
					if(strpos($_SERVER['HTTP_REFERER'], 'login') || strpos($_SERVER['HTTP_REFERER'], 'auth') || $url['path'] == "/"){
						$this->respon['auth']['HTTP_REFERER'] = '/dashboard';
					}
				} else {
					$this->respon['status'] = NG;
				}
			} catch (Exception $e){
				$this->respon['status'] = EX;
				$this->respon['Exception'] = $e->getMessage();
			}
			$this->getHelper('json')->sendJson($this->respon);
		}

		if($this->getRequest()->isGet()) {
			$this->redirect('/login');
		}
	}

}