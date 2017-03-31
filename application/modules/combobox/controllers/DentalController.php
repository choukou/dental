<?php

class Combobox_DentalController extends Combobox_AppController {
	public function init() {
		parent::init();
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	}
	public function indexAction(){
		$this->getHelper('json')->sendJson($respon);
	}

}
