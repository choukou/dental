<?php
/**
 *  Sample Foo Resource
 */
class Api_FooController extends REST_Controller
{
	public function indexAction() {
		$this->view->message = 'indexAction has been called.';
		$this->_response->ok();
	}

	public function headAction() {
		$this->view->message = 'headAction has been called';
		$this->_response->ok();
	}

	public function getAction() {
		$id = $this->_getParam('id', 0);

		$this->view->id = $id;
		$this->view->message = sprintf('Resource #%s', $id);
		$this->_response->ok();
	}

	public function postAction() {
		$this->view->params = $this->_request->getParams();
		$this->view->message = 'Resource Created';
		$this->_response->created();
	}


	public function putAction() {
		$id = $this->_getParam('id', 0);

		$this->view->id = $id;
		$this->view->params = $this->_request->getParams();
		$this->view->message = sprintf('Resource #%s Updated', $id);
		$this->_response->ok();
	}

	public function deleteAction() {
		$id = $this->_getParam('id', 0);

		$this->view->id = $id;
		$this->view->message = sprintf('Resource #%s Deleted', $id);
		$this->_response->ok();
	}
}
