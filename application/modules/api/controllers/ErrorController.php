<?php
/**
 * RESTful ErrorController
 *
 **/
class Api_ErrorController extends REST_Controller
{
	public function errorAction()
	{
		$this->_helper->layout()->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
		$this->rsp->Status = NG;
		$this->getResponse()->setHeader('Content-type', 'application/json');

		if ($this->_request->hasError()) {
			$error = $this->_request->getError();
			$this->rsp->Message = $error->message;
			$this->getResponse()->setHttpResponseCode($error->code);
			$this->view->assign((array)$this->rsp);
			return;
		}

		$errors = $this->_getParam('error_handler');

		if (!$errors || !$errors instanceof ArrayObject) {
			$this->rsp->Message = 'You have reached the error page';
			$this->view->assign((array)$this->rsp);
			return;
		}

		switch ($errors->type) {
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
				// 404 error -- controller or action not found
				$this->rsp->Message = 'Page not found';
				$this->getResponse()->setHttpResponseCode(404);
				break;

			default:
				// application error
				$this->rsp->Message = 'Application error';
				$this->getResponse()->setHttpResponseCode(500);

				break;
		}

		// conditionally display exceptions
		if ($this->getInvokeArg('displayExceptions') == true) {
			$this->rsp->Exception= $errors->exception->getMessage();
		}

		$this->view->assign((array)$this->rsp);

	}

	/**
	 * Catch-All
	 * useful for custom HTTP Methods
	 *
	 **/
	public function __callAction()
	{
	}

	/**
	 * Index Action
	 *
	 * @return void
	 */
	public function indexAction()
	{
	}

	/**
	 * GET Action
	 *
	 * @return void
	 */
	public function getAction()
	{
	}

	/**
	 * POST Action
	 *
	 * @return void
	 */
	public function postAction()
	{
	}

	/**
	 * PUT Action
	 *
	 * @return void
	 */
	public function putAction()
	{
	}

	/**
	 * DELETE Action
	 *
	 * @return void
	 */
	public function deleteAction()
	{
	}
}

