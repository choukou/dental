<?php

class Api_ChemicalController extends REST_Controller {
	public function init() {
		parent::init();
	}

	public function indexAction() {
		$this->forward('get');
// 		$this->_response->ok();
	}

	public function getAction() {
		$rsp = $this->db->callProcedurePrepare('chemical_fnd', $this->user->company_id);
		$this->rsp->Data = $rsp->first();
		$this->view->assign((array)$this->rsp);
		$this->_response->ok();
	}

	public function postAction() {
		$params = $this->getRequestParams(array('datajson'));
		if($this->isJson($params['datajson'])) {
			$rsp = $this->db->callProcedurePrepare('chemical_act1', $params);
			$this->view->assign((array)$rsp->getStatus());
		}

		$this->_response->created();
	}

}
