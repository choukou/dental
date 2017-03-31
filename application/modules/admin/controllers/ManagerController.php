<?php

class Admin_ManagerController extends Admin_AppController {
	/**
	 * @author
	 * @package   Base
	 * @return    user, model
	 */
	public function init() {
		parent::init();
		if($this->user['role_id'] != 3){
			$this->redirect('/');
		}
	}
	/**
	 * index home
	 */
	public function indexAction(){
		$this->view->title = 'Setting Package';
		//
		$rsp = $this->db->callProcedurePrepare('package_info');
		$this->view->data = $rsp[0];
	}

	public function rateAction(){
		$this->view->title = 'Setting Rate';
		//
		$rsp = $this->db->callProcedurePrepare('rate_info');
		$this->view->data = $rsp[0];
	}

	public function listuserAction(){
		$this->view->title = 'List User';
		//
		$username = $this->getParam('user_name', '');
		$params = array('user_name' => $username);
		$rsp = $this->db->callProcedurePrepare('listuser_info', $params);
		$this->view->data = $rsp[0];
		if($this->getRequest()->isPost()){
			$this->_helper->layout()->disableLayout();
			$this->render('listusertable');
		}else {
			$this->render('listuser');
		}
	}

	public function listghAction(){
		$this->view->title = 'List Withdraw';
		//
		if($this->getRequest()->isPost()){

		}

		if($this->getRequest()->isGet()){
			try {
				$Blockchain =  new \Blockchain\Blockchain(BLC::API_CODE);
				$Blockchain->setServiceUrl(BLC::SERVICE_URL);
				$Blockchain->Wallet->credentials(BLC::getID(), BLC::getPWD(), BLC::WALLET_PWD2);
				$balance = $Blockchain->Wallet->getAddressBalance(BLC::STOCK_ID);
				$bl = $balance->balance;
			} catch (Exception $e) {
				$bl = 0;

			}

			$this->view->balance = $bl;

			$rsp_total = $this->db->callProcedurePrepare('admin_get_total_gh_bit');
			$rsp_list = $this->db->callProcedurePrepare('admin_list_gh', array('2000-01-01'));

			$total_bit = 0;
			if(isset($rsp_total[0][0]['all_bit'])) {
				$total_bit = $rsp_total[0][0]['all_bit'];
			}

// 			Zend_Debug::dump($rsp_list[0]);die;
			$this->view->total_bit = $total_bit;
			$this->view->tranfr_bit = $bl < $total_bit ? $total_bit - $bl : 0 ;
			$this->view->list =  isset($rsp_list[0]) ? $rsp_list[0] : array();
		}
	}


	public function savepkAction() {
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		if($this->getRequest()->isPost()){
			$respon['status'] = OK;
			$params = $this->getAllParams(true);
			$params['user_id'] = $this->user['user_id'];
			$params['ip'] = $_SERVER['REMOTE_ADDR'];
			try {
			$rsp = $this->db->callProcedurePrepare('admin_save_pk', $params);
			$this->checkExcepion($rsp);
			if(!isset($rsp[0][0]['success'])){
				$respon['status'] = NG;
				$respon['msg'] = $rsp[0][0]['msg'];
			}

			} catch (Exception $e){
				$respon['status'] = EX;
				$respon['Exception'] = $e->getMessage();
			}
			$this->getHelper('json')->sendJson($respon);
		}
	}

	public function saverateAction() {
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		if($this->getRequest()->isPost()){
			$respon['status'] = OK;
			$params = $this->getAllParams(true);
			$params['user_id'] = $this->user['user_id'];
			$params['ip'] = $_SERVER['REMOTE_ADDR'];
			try {
			$rsp = $this->db->callProcedurePrepare('admin_save_rate', $params);
			$this->checkExcepion($rsp);
			if(!isset($rsp[0][0]['success'])){
				$respon['status'] = NG;
				$respon['msg'] = $rsp[0][0]['msg'];
			}

			} catch (Exception $e){
				$respon['status'] = EX;
				$respon['Exception'] = $e->getMessage();
			}
			$this->getHelper('json')->sendJson($respon);
		}
	}

	public function updatestatusAction() {
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		if($this->getRequest()->isPost()){
			$respon['status'] = OK;
			$params = $this->getAllParams(true);
			$params['update_user_id'] = $this->user['user_id'];
			$params['ip'] = $_SERVER['REMOTE_ADDR'];
			try {
			$rsp = $this->db->callProcedurePrepare('admin_save_status', $params);
			$this->checkExcepion($rsp);
			if(!isset($rsp[0][0]['success'])){
				$respon['status'] = NG;
				$respon['msg'] = $rsp[0][0]['msg'];
			}

			} catch (Exception $e){
				$respon['status'] = EX;
				$respon['Exception'] = $e->getMessage();
			}
			$this->getHelper('json')->sendJson($respon);
		}
	}

	public function walletadminAction() {
		$this->view->title = 'Wallet Admin';

		try {
			$Blockchain =  new \Blockchain\Blockchain(BLC::API_CODE);
			$Blockchain->setServiceUrl(BLC::SERVICE_URL);
			$Blockchain->Wallet->credentials(BLC::getID(), BLC::getPWD(), BLC::WALLET_PWD2);
			$balance = $Blockchain->Wallet->getAddressBalance(BLC::STOCK_ID);
			$bl = $balance->balance;
		} catch (Exception $e) {
			$bl = 0;

		}

		if($this->getRequest()->isGet()){
			$this->view->balance = $bl;
		}

		if($this->getRequest()->isPost()){
			$respon['status'] = OK;
			$nb_bit = $this->getParam('nb_bit', 0);
			$rsp = $this->db->callProcedurePrepare('admin_get_wallet');
			if(is_numeric($nb_bit) && $nb_bit > ($bl - 0.00001)) {
				$respon['status'] = NG;
				$respon['msg'] = 'Not enough bit';
			}

			if(empty($rsp[0][0]['sys_value'])) {
				$respon['status'] = NG;
				$respon['msg'] = 'Not exists wallet admin';
			}

			if(is_numeric($nb_bit) && $nb_bit > 0 && !empty($rsp[0][0]['sys_value']) && $nb_bit < ($bl - 0.00001) && $this->isAdmin()) {
				$wallet = $rsp[0][0]['sys_value'];

				if($bl < 0.00001) {
					$respon['status'] = NG;
					$respon['msg'] = 'Not enough bit';
				} else {
					try {
						$response = $Blockchain->Wallet->send($wallet, $nb_bit, BLC::STOCK_ID, BLC::FEE, "Receive");
					} catch (Exception $e) {
						$respon['status'] = NG;
						$respon['msg'] = 'Unsuccessful!';
					}
				}

			} else {
				$respon['status'] = NG;
				$respon['msg'] = 'Unsuccessful!';
			}

			$this->getHelper('json')->sendJson($respon);
		}


	}

	public function settingshomepageAction() {
		$this->view->title = 'Settings';
		if($this->getRequest()->isGet()){
			$homeData = $this->db->callProcedurePrepare('home_default');
// 			Zend_Debug::dump($homeData);die;
			$this->view->homedata = $homeData[0];
		}
	}

	public function savesetingAction() {
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		if($this->getRequest()->isPost()){
			$respon['status'] = OK;
			$params = $this->getAllParams(true);
			unset($params['_wysihtml5_mode']);
// 			var_dump($params);die;
// 			$params['user_id'] = $this->user['user_id'];
// 			$params['ip'] = $_SERVER['REMOTE_ADDR'];
			try {
				$rsp = $this->db->callProcedurePrepare('admin_save_setting', $params);
// 				$sql = $this->db->getSql();
// 				var_dump($sql);die;
				$this->checkExcepion($rsp);
				if(!isset($rsp[0][0]['success'])){
					$respon['status'] = NG;
					$respon['msg'] = $rsp[0][0]['msg'];
				}

			} catch (Exception $e){
				$respon['status'] = EX;
				$respon['Exception'] = $e->getMessage();
			}
			$this->getHelper('json')->sendJson($respon);
		}
	}


}
