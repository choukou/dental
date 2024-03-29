<?php

class REST_Controller_Plugin_ErrorHandler extends Zend_Controller_Plugin_Abstract
{
	public function routeShutdown(Zend_Controller_Request_Abstract $request)
	{
		$frontController = Zend_Controller_Front::getInstance();

		$error = $frontController->getPlugin('Zend_Controller_Plugin_ErrorHandler');

		$error->setErrorHandlerModule($request->getModuleName());
	}
}