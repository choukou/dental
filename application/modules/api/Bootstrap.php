<?php
/**
 * Api Bootstrap
 *
 * @author        GiangNT
 * @package       Api Module
 *
 */

class Api_Bootstrap extends Zend_Application_Module_Bootstrap
{

	protected function _initAutoload() {
		$moduleLoader = new Zend_Application_Module_Autoloader(array(
					'namespace' => 'Api',
					'basePath' => APPLICATION_PATH . '/modules/api'
					));

		$resourceLoader = new Zend_Loader_Autoloader_Resource ( array (
				'basePath' => APPLICATION_PATH . '/modules/api',
				'namespace' => '',
				'resourceTypes' => array (
						'controller' => array (
								'path' => '/controllers',
								'namespace' => 'Api_'
						)
				)
		));

		return;
	}

	public function _initREST() {
		$frontController = Zend_Controller_Front::getInstance();

		// register the RestHandler plugin
		$frontController->registerPlugin(new REST_Controller_Plugin_RestHandler($frontController));

		$frontController->registerPlugin(new REST_Controller_Plugin_ErrorHandler($frontController));

		// add REST contextSwitch helper
		$contextSwitch = new REST_Controller_Action_Helper_ContextSwitch();
		Zend_Controller_Action_HelperBroker::addHelper($contextSwitch);

		// add restContexts helper
		$restContexts = new REST_Controller_Action_Helper_RestContexts();
		Zend_Controller_Action_HelperBroker::addHelper($restContexts);
	}
}
