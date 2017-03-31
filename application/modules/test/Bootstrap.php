<?php
/**
 * Test Bootstrap
 *
 * @author        GiangNT
 * @package       Auth Module
 *
 */

class Test_Bootstrap extends Zend_Application_Module_Bootstrap
{

	protected function _initAutoload() {
		$moduleLoader = new Zend_Application_Module_Autoloader(array(
					'namespace' => 'Test',
					'basePath' => APPLICATION_PATH . '/modules/test'
					));

		$resourceLoader = new Zend_Loader_Autoloader_Resource ( array (
				'basePath' => APPLICATION_PATH . '/modules/test',
				'namespace' => '',
				'resourceTypes' => array (
						'controller' => array (
								'path' => '/controllers',
								'namespace' => 'Test_'
						)
				)
		));

		return;
	}

}
