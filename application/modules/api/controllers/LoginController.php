<?php
/**
 *  Sample Foo Resource
 */
class Api_LoginController extends REST_Controller
{

	public function init() {
		parent::init();
	}

	public function indexAction() {
		$key_params = array('username', 'password');
		$params = $this->getRequestParams($key_params);
		$params['password'] = $this->getPassword($params['password']);
		$params['ip'] = $_SERVER['REMOTE_ADDR'];

		$rsp = $this->db->callProcedurePrepare('login', $params);
		$this->view->assign($rsp->firstRow());
		$this->_response->ok();
	}

	public function getAction() {
		$this->_response->ok();
	}
}
