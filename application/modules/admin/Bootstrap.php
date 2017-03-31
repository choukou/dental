<?php
/**
 * Master Bootstrap
 *
 * @author        GiangNT
 * @package       Admin Module
 *
 */

class Admin_Bootstrap extends Zend_Application_Module_Bootstrap
{

	protected function _initAutoload() {
		$moduleLoader = new Zend_Application_Module_Autoloader(array(
					'namespace' => 'Admin',
					'basePath' => APPLICATION_PATH . '/modules/admin'
					));

		$resourceLoader = new Zend_Loader_Autoloader_Resource ( array (
				'basePath' => APPLICATION_PATH . '/modules/admin',
				'namespace' => '',
				'resourceTypes' => array (
						'controller' => array (
								'path' => '/controllers',
								'namespace' => 'Admin_'
						)
				)
		));

		return;
	}

}
