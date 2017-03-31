<?php
/**
 * Master Bootstrap
 *
 * @author        GiangNT
 * @package       Auth Module
 *
 */
 
class Master_Bootstrap extends Zend_Application_Module_Bootstrap
{

	protected function _initAutoload() {
		$moduleLoader = new Zend_Application_Module_Autoloader(array(
					'namespace' => 'Master',
					'basePath' => APPLICATION_PATH . '/modules/master'
					));

		$resourceLoader = new Zend_Loader_Autoloader_Resource ( array (
				'basePath' => APPLICATION_PATH . '/modules/master',
				'namespace' => '',
				'resourceTypes' => array (
						'controller' => array (
								'path' => '/controllers',
								'namespace' => 'Master_'
						)
				)
		));
		
		return;
	}

}
