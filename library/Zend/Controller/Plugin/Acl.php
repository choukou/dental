<?php
/**
 * Medicare Project
 * PHP 5
 * @author      : GiangNT
 * @copyright   : Copyright (c) ANS-ASIA
 * @package     : ACL
 */
class Zend_Controller_Plugin_Acl extends Zend_Controller_Plugin_Abstract {
	private $_acl;
	private $_auth;
	private $_user;
	private $_db;

	public function __construct (){
		$this->_acl   = new Zend_Acl();
		$this->_auth  = Zend_Auth::getInstance();
		$model = new Model_DBCommon();
		$this->_db = $model->getDb();
	}

	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		try{
// 			$roles = $this->_db->callProcedurePrepare('get_roles');
// 			$acls = $this->_db->callProcedurePrepare('get_acls');
			$roles = $this->getDataFromJson('role.jon');
			$acls = $this->getDataFromJson('acl.jon');

			if(!$this->_auth->hasIdentity()) {
				return;
			}
			$this->_user = $this->_auth->getIdentity();
			$user_role = strtoupper($this->_user['role_name']);

			$m_name = strtoupper($request->getModuleName());
			$c_name = strtoupper($request->getControllerName());
			$a_name = strtoupper($request->getActionName());
			$current_rc = $this->creatResource($m_name, $c_name, $a_name);

			// Add a new role
			$guest = 'GUEST';
			if(!$this->_acl->hasRole($guest)) {
				$this->_acl->addRole(new Zend_Acl_Role($guest));
			}

			$parents = array($guest);
			foreach ($roles[0] as $role) {
				$role_name= strtoupper($role['role_name']);
				if(!$this->_acl->hasRole($role_name)) {
					$this->_acl->addRole(new Zend_Acl_Role($role_name), $parents);
				}
			}
			foreach ($acls[0] as $resource) {
				$role = strtoupper($resource['role_name']);
				$rc = $this->creatResource($resource['module'], $resource['controller'], $resource['action']);
				if(!$this->_acl->has($rc)) {
					$this->_acl->add(new Zend_Acl_Resource($rc));
				}
				if($resource['active'] == 1) {
					$this->_acl->allow($role, $rc);
				} else {
					$this->_acl->deny($role, $rc);
				}
			}
			if(!$this->_acl->has($current_rc)) {
				// return $this->permissionDeny();
				return;
			}

			if(!$this->_acl->isAllowed($user_role, $current_rc)) {
				return $this->permissionDeny();
			}
		} catch(Exception $e) {
			Zend_Debug::dump($e);die;
			return $this->permissionDeny();
		}
	}

	protected function permissionDeny() {
		return Zend_Controller_Action_HelperBroker::getStaticHelper('redirector')->setGotoUrl('/error/Error/page403');
	}

	private function creatResource($m_name, $c_name, $a_name){
		return  strtolower($m_name. '.' . $c_name . '.' . $a_name);
	}

	private function getDataFromJson($filename) {
		$filename = APPLICATION_PATH . DS . 'configs'. DS . $filename;
		return json_decode(file_get_contents($filename), true);
	}
}

?>
