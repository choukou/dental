<?php
/**
 * Combobox Bootstrap
 *
 * @author        GiangNT
 * @package       Combobox Module
 *
 */

class Combobox_Bootstrap extends Zend_Application_Module_Bootstrap
{

	protected function _initAutoload() {
		$moduleLoader = new Zend_Application_Module_Autoloader(array(
					'namespace' => 'Combobox',
					'basePath' => APPLICATION_PATH . '/modules/combobox'
					));

		$resourceLoader = new Zend_Loader_Autoloader_Resource ( array (
				'basePath' => APPLICATION_PATH . '/modules/combobox',
				'namespace' => '',
				'resourceTypes' => array (
						'controller' => array (
								'path' => '/controllers',
								'namespace' => 'Combobox_'
						)
				)
		));

		return;
	}

}
