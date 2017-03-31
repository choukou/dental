<?php
class Auth_Bootstrap extends Zend_Application_Module_Bootstrap
{
	protected function _initAutoload() {
		$moduleLoader = new Zend_Application_Module_Autoloader(array(
					'namespace' => 'Auth',
					'basePath' => APPLICATION_PATH . '/modules/auth'
					));

		$resourceLoader = new Zend_Loader_Autoloader_Resource ( array (
				'basePath' => APPLICATION_PATH . '/modules/auth',
				'namespace' => '',
				'resourceTypes' => array (
						'controller' => array (
								'path' => '/controllers',
								'namespace' => 'Auth_'
						)
				)
		));
		return;
	}

}
