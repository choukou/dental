<?php

class Master_AuthController extends Master_AppController {
	protected $respon;

	/**
	 * @author
	 * @package   Base
	 * @return    user, model
	 */
	public function init() {
		parent::init();
		$this->respon['status'] = OK;
	}
	/**
	 * index home
	 */
	public function indexAction(){
		$this->view->title = 'Login';
		$this->_helper->layout()->disableLayout();
		if ($this->auth->hasIdentity()) {
			$this->auth->clearIdentity();
		}
		$this->view->captcha = $this->getRandom();

	}

	public function loginAction(){
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

	/**
	 * logoutAction
	 *
	 * @author GiangNT
	 */
	public function logoutAction() {
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		if ($this->auth->hasIdentity()) {
			$this->auth->clearIdentity();
		}
		$this->_redirect('/');
	}
	public function profileAction(){
		$user_id = $this->user['user_id'];
		$share = '/register';
		if(isset($user_id) && $user_id !=0){
			$strWhere	=	'del_flag = 0 and user_id = '.$user_id;
			$profile		=	$this->model->selectDB('m001', array('*'),$strWhere);
			if(isset($profile) && count($profile) >0){
				$this->view->profile = $profile[0];
				$share .= '/' . base64_encode($profile[0]['user_name'].SECRET);
			}else{
				$this->view->profile = array();
			}
			$this->view->share = $share;
		}
	}
	public function inviteAction(){
		$this->view->title = 'Link Referral';
		$user_id = $this->user['user_id'];
		$share = '/register';
		if(isset($user_id) && $user_id !=0){
			$strWhere	=	'del_flag = 0 and user_id = '.$user_id;
			$profile		=	$this->model->selectDB('m001', array('*'),$strWhere);
			if(isset($profile) && count($profile) >0){
				$share .= '/' . base64_encode($profile[0]['user_name'].SECRET);
			}
			$this->view->share = $share;
		}
	}
	public function updateaccountAction(){
		try {
			$user_id			=	$this->_getParam('user_id', 0);
			$email				=	$this->_getParam('email', '');
			$phone				=	$this->_getParam('phone', '');
			$wallet				=	$this->_getParam('wallet', '');
			$code				=	$this->_getParam('code', '');
		//	$t_password			=	$this->getPassword($this->_getParam('t_password', ''));
			$data	=	array(
						'email'				=>	$email
					,	'phone'				=>	$phone
					,	'wallet'				=>	$wallet
					,	'ip_update'			=>	$_SERVER['REMOTE_ADDR']
					,	'update_date'		=>	date('Y-m-d H:i:s')
					,	'user_updated_id'	=>	$this->user['user_id']
					,	'reset_pass_code'	=> ''
					,	'del_flag'			=> 0
			);
			$count = 0;
			if($user_id != 0){
				$strWhere = 'user_id = '.$user_id.' and reset_pass_code= "'.$code.'"';
				$count = $this->model->getCount('m001', 'user_id',$strWhere);
				if($count > 0){
					$this->model->updateDB('m001', $data,$strWhere);
				}
			}
			exit($count);
		} catch (Exception $e) {
			exit(NG);
		}
	}

	public function sendcodeAction(){
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		if($this->getRequest()->isPost()){
			$params['email'] = $this->user['email'];
			$params['reset_code'] = $this->getRandom();
			$params['ip'] = $_SERVER['REMOTE_ADDR'];
			try {
				// check_exists_email and save reset code
				$rsp = $this->db->callProcedurePrepare('check_exists_email', $params);
				if($rsp[0][0]['status'] != 1){
					$this->respon['status'] = NG;
				} else {
					$mail = $this->sendMail($params['email'], 'Change Information!',  $params['reset_code']);
				}
			} catch (Exception $e){
				$this->respon['status'] = EX;
				$this->respon['Exception'] = $e->getMessage();
			}
			$this->getHelper('json')->sendJson($this->respon);
		}
	}
	public function updatepasswordAction(){
		try {
			$user_id			=	$this->_getParam('user_id',0);
			$oldpassword		=	$this->_getParam('old_password', '');
			$newpassword		=	$this->_getParam('new_password', '');
			$data	=	array(
						'password'			=>	$this->getPassword($newpassword)
					,	'ip_update'			=>	$_SERVER['REMOTE_ADDR']
					,	'update_date'		=>	date('Y-m-d H:i:s')
					,	'user_updated_id'	=>	$user_id
					,	'del_flag'			=> 	0
			);
			$count = 0;
			if($user_id != 0){
				$strWhere = 'user_id = '.$user_id.' and password = "'.$this->getPassword($oldpassword).'"';
				$count = $this->model->getCount('m001', 'user_id',$strWhere);
				if($count > 0){
					$this->model->updateDB('m001', $data,$strWhere);
				}
			}
			exit($count);
		} catch (Exception $e) {
			exit(NG);
		}
	}
	public function updatetpasswordAction(){
		try {
			$user_id			=	$this->_getParam('user_id',0);
			$oldtpassword		=	$this->_getParam('old_tpassword', '');
			$newtpassword		=	$this->_getParam('new_tpassword', '');
			$data	=	array(
						't_password'		=>	$this->getPassword($newtpassword)
					,	'ip_update'			=>	$_SERVER['REMOTE_ADDR']
					,	'update_date'		=>	date('Y-m-d H:i:s')
					,	'user_updated_id'	=>	$user_id
					,	'del_flag'			=> 	0
			);
			$count = 0;
			if($user_id != 0){
				$strWhere = 'user_id = '.$user_id.' and t_password="'.$this->getPassword($oldtpassword).'"';
				$count = $this->model->getCount('m001', 'user_id',$strWhere);
				if($count > 0){
					$this->model->updateDB('m001', $data,$strWhere);
				}
			}
			exit($count);
		} catch (Exception $e) {
			exit(NG);
		}
	}
	public function updatebankAction(){
		try {
			$user_id				=	$this->_getParam('user_id',0);
			$bank_acc_id			=	$this->_getParam('bank_acc_id', 0);
			$bank_name				=	$this->_getParam('bank_name', '');
			$bank_branch_name		=	$this->_getParam('bank_branch_name', '');
			$bank_acc_name			=	$this->_getParam('bank_acc_name', '');
			$bank_acc_number		=	$this->_getParam('bank_acc_number', '');
			$bank_link_phone		=	$this->_getParam('bank_link_phone', '');
			$bank_t_password		=	$this->_getParam('bank_t_password', '');
			$data	=	array(
						'bank_name'				=> 	$bank_name
					,	'bank_branch_name'		=> 	$bank_branch_name
					,	'bank_acc_name'			=> 	$bank_acc_name
					,	'bank_acc_number'		=> 	$bank_acc_number
					,	'bank_link_phone'		=> 	$bank_link_phone
					,	'del_flag'				=> 	0
			);
			$count = 0;
			if($bank_acc_id != 0){
				$data['ip_update'] = $_SERVER['REMOTE_ADDR'];
				$data['update_date'] = date('Y-m-d H:i:s');
				$data['user_updated_id'] = $user_id;
				$strWhere = 'user_id = '.$user_id.' and bank_acc_id='.$bank_acc_id;
				$count	=	$this->model->getCount('m002', 'bank_acc_id',$strWhere);
				if($count > 0){
					$this->model->updateDB('m002', $data,$strWhere);
				}
			}else{
				$data['ip_create'] 			= $_SERVER['REMOTE_ADDR'];
				$data['create_date'] 		= date('Y-m-d H:i:s');
				$data['user_created_id'] 	= $user_id;
				$count	=	$this->model->insertDB('m002', $data);
			}
			exit($count);
		} catch (Exception $e) {
			exit(NG);
		}
	}

	public function resetpasswordAction(){
		$this->view->title = 'Reset Password';
		$this->_helper->layout()->disableLayout();
		if ($this->auth->hasIdentity()) {
			$this->auth->clearIdentity();
		}

		if($this->getRequest()->isPost()){
			$params = $this->getAllParams(true);
			$params['reset_code'] = $this->getRandom();
			$params['ip'] = $_SERVER['REMOTE_ADDR'];
			try {
				// check_exists_email and save reset code
				$rsp = $this->db->callProcedurePrepare('check_exists_email', $params);
				$this->respon['email'] = $params['email'];
				if($rsp[0][0]['status'] != 1){
					$this->respon['status'] = NG;
				} else {
					$mail = $this->sendMail($params['email'], 'Reset Password',  $params['reset_code']);
				}
			} catch (Exception $e){
				$this->respon['status'] = EX;
				$this->respon['Exception'] = $e->getMessage();
			}
			$this->getHelper('json')->sendJson($this->respon);
		}
	}

	public function changepasswordAction() {
		$this->view->title = 'Change Password';
		$this->_helper->layout()->disableLayout();
		if($this->getRequest()->isPost()){
			$params = $this->getAllParams(true);
			$params['ip'] = $_SERVER['REMOTE_ADDR'];
			try {
				if(!$this->checkSame($params['password'], $params['password2'])){
					throw new Exception('error_pwd_match');
				}
				unset($params['password2']);
				$params['password'] = $this->getPassword($params['password']);
				$rsp = $this->db->callProcedurePrepare('change_password', $params);
// 				$sql = $this->db->getSql();
// 				var_dump($rsp);die;
				if($rsp[0][0]['status'] != 1){
					$this->respon['status'] = NG;
				}
			} catch (Exception $e){
				$this->respon['status'] = EX;
				$this->respon['Exception'] = $e->getMessage();
			}
			$this->getHelper('json')->sendJson($this->respon);
		}
	}
}