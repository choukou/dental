<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
	protected function _initAutoload() {
// 		$resourceLoader = new Zend_Loader_Autoloader_Resource(array(
// 			'basePath' => APPLICATION_PATH,
// 			'namespace' => '',
// 			'resourceTypes' => array(
// 				'model' => array(
// 					'path' => 'models/',
// 					'namespace' => 'Model_'
// 				)
// 			)
// 		));

		Zend_Loader::loadFile('Respon.php', LIBRARY_PATH . '/Base');

	}

	protected function _initREST() {
		$frontController = Zend_Controller_Front::getInstance();

		// set custom request object
		$frontController->setRequest(new REST_Request);
		$frontController->setResponse(new REST_Response);

// 		// add the REST route for module only
		$restRoute = new Zend_Rest_Route($frontController, array(), array('api'));
		$frontController->getRouter()->addRoute('rest', $restRoute);
	}
	protected function _initDb() {
		try {
			$this->_config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/database.ini', 'database');
			$config = new Zend_Config(
					array(
							'database' => array(
									'adapter' => $this->_config->database->adapter,
									'params'  => $this->_config->database->params->toArray()
							)
					)
					);

			// Instantiate the DB factory
			$dbAdapter = Zend_Db::factory($config->database);
			Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);
		} catch (PDOException $e) {
			echo "There was an error querying the database <br />" . $e->getMessage();
		} catch (Zend_Db_Adapter_Exception $e) {
			echo "Unable to connect to the database <br />" . $e->getMessage();
		} catch (Exception $e) {
			die($e->getMessage());
		}
	}

	protected function _initLog() {
		if ($this->hasPluginResource("log")) {

			$r = $this->getPluginResource("log");
			$log = $r->getLog();

			$db = Zend_Db_Table::getDefaultAdapter();

			$columnMapping = array('lvl' => 'priority', 'msg' => 'message', 'timestampFormat' => 'timestamp');
			$writer = new Zend_Log_Writer_Db($db, "sys_log", $columnMapping);
			$log->addWriter($writer);
			Zend_Registry::set("log", $log);
// 			return $log;
		}
	}

	protected function _initSession() {
		$options = $this->getOptions();
		Zend_Session::setOptions($options['resources']['session']);

// 		$config = array(
// 				'name'           => 'session',
// 				'primary'        => 'id',
// 				'modifiedColumn' => 'modified',
// 				'dataColumn'     => 'data',
// 				'lifetimeColumn' => 'lifetime'
// 		);
// 		Zend_Session::setSaveHandler(new Zend_Session_SaveHandler_DbTable($config));
		Zend_Session::start();
	}

// 	protected function _initDoctype() {
// 		$this->bootstrap('view');
// 		$view = $this->getResource('view');
// 		$view->doctype('HTML5');
// 	}

	protected function _initRewrite() {
		$front = Zend_Controller_Front::getInstance();
		$router = $front->getRouter();
		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini', 'routers');

		if(isset($config->routes)) {
				$router->addConfig($config,'routes');
		}

	}

}
?>
