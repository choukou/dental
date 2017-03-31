<?php

class ErrorController extends Zend_Controller_Action
{

	public function errorAction()
	{
		$this->_helper->layout->disableLayout();

		$errors = $this->_getParam('error_handler');

		switch ($errors->type) {
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

				// 404 error -- controller or action not found
				$this->getResponse()->setHttpResponseCode(404);
				$this->view->message = 'Page not found';
				break;
			default:
				// application error
				$this->getResponse()->setHttpResponseCode(500);
				$this->view->message = 'Application error';
				break;
		}

		// Log exception, if logger available
		if ($log = $this->getLog()) {
				$log->crit($this->view->message, $errors->exception);
		}

		// conditionally display exceptions
		if ($this->getInvokeArg('displayExceptions') == true) {
			$this->view->exception = $errors->exception;
		}

		$this->view->request   = $errors->request;

		$this->_helper->viewRenderer->setNoRender();
		$this->getHelper('json')->sendJson(404);
	}

	public function getLog()
	{
// 		$bootstrap = $this->getInvokeArg('bootstrap');
// 		if (!$bootstrap->hasPluginResource('log')) {
// 			return false;
// 		}
// 		$log = $bootstrap->getResource('log');
		if (!Zend_Registry::isRegistered('log')){
			return false;
		}
		$log = Zend_Registry::get("log");
		return $log;
	}


}
