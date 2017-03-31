<?php
class Auth_LoginController extends Auth_AppController {

	public function init() {
		parent::init();
	}

	public function indexAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		if ($this->auth->hasIdentity()) {
			$this->auth->clearIdentity();
		}
		$this->redirect('/');
	}

}