<?php
class Test_ApiController extends Test_AppController {
	public function init() {
		parent::init();

	}

	public function indexAction() {
		$this->_helper->layout->disablelayout();
		$domain = $this->getRequest()->getScheme() . '://' . $this->getRequest()->getHttpHost();
		$title = 'Test';
		$apis = array(
				$domain."/api/login"=> 'username,password',
				$domain."/api/chemical"=> 'datajson,token',
				$domain."/api/chemical/get" => 'token',
				$domain."/api/timemark"=> 'datajson,token',
				$domain."/api/timemark/get" => 'token',
				$domain."/api/drug"=> 'datajson,token',
				$domain."/api/drug/get" => 'token',
		);

		$type = array(
			$domain."/api/login" => 'get',
			$domain."/api/chemical" => 'post',
			$domain."/api/chemical/get" => 'get',
			$domain."/api/timemark" => 'post',
			$domain."/api/timemark/get" => 'get',
			$domain."/api/drug" => 'post',
			$domain."/api/drug/get" => 'get',
		);
		$note = array(
			$domain."/api/login" => 'Type ajax = GET',
			$domain."/api/chemical" => 'Type ajax = POST',
			$domain."/api/chemical/get" => 'Type ajax = GET',
			$domain."/api/timemark" => 'Type ajax = POST',
			$domain."/api/timemark/get" => 'Type ajax = GET',
			$domain."/api/drug" => 'Type ajax = POST',
			$domain."/api/drug/get" => 'Type ajax = GET',

		);
		ksort($apis);
		$this->view->apis = $apis;
		$this->view->domain = $domain;
		$this->view->title = $title;
		$this->view->note = $note;
		$this->view->type = $type;
	}

	public function parsejsonAction() {
		$this->_helper->layout->disablelayout();
		$this->_helper->viewRenderer->setNoRender();
		$data = $this->getAllParams(true);
		Zend_Debug::dump($data);
// 		die;
	}
}
