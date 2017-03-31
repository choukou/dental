<?php
/**
 * REST Controller default actions
*
*/
abstract class REST_Controller extends Zend_Controller_Action
{
	protected $db;
	protected $user;
	protected $rsp;
	private $_allow = array('login');

	public function init() {
		$this->db = Zend_Db_Table::getDefaultAdapter();
		$this->user = new user();
		$this->rsp = new Respon();
		if(!in_array(strtolower($this->getRequest()->getControllerName()), $this->_allow)) {
			$this->hasToken();
		}
	}


	/**
	 * The index action handles index/list requests; it should respond with a
	 * list of the requested resources.
	 */
	public function indexAction() {
		$this->notAllowed();
	}

	/**
	 * The get action handles GET requests and receives an 'id' parameter; it
	 * should respond with the server resource state of the resource identified
	 * by the 'id' value.
	 */
	public function getAction() {
		$this->notAllowed();
	}

	/**
	 * The post action handles POST requests; it should accept and digest a
	 * POSTed resource representation and persist the resource state.
	 */
	public function postAction() {
		$this->notAllowed();
	}

	/**
	 * The put action handles PUT requests and receives an 'id' parameter; it
	 * should update the server resource state of the resource identified by
	 * the 'id' value.
	 */
	public function putAction() {
		$this->notAllowed();
	}

	/**
	 * The delete action handles DELETE requests and receives an 'id'
	 * parameter; it should update the server resource state of the resource
	 * identified by the 'id' value.
	 */
	public function deleteAction() {
		$this->notAllowed();
	}

	/**
	 * The head action handles HEAD requests; it should respond with an
	 * identical response to the one that would correspond to a GET request,
	 * but without the response body.
	 */
	public function headAction() {
		$this->_forward('get');
	}

	/**
	 * The options action handles OPTIONS requests; it should respond with
	 * the HTTP methods that the server supports for specified URL.
	 */
	public function optionsAction() {
		$this->_response->setBody(null);
		$this->_response->setHeader('Allow', $this->_response->getHeaderValue('Access-Control-Allow-Methods'));
		$this->_response->ok();
	}

	protected function notAllowed() {
		$this->_response->setBody(null);
		$this->_response->notAllowed();
	}

	private function hasToken() {
		$token= $this->getParam('token', '');
		$rsp = $this->db->callProcedurePrepare('hasToken', $token);
		if($rsp->hasField('token')) {
			$this->user = (object)$rsp->firstRow();
		} else {
			$this->_request->dispatchError(200, "Missing token: $token");
		}

	}
	public function getRequestParams($params = array()) {
		if(empty($params)) {
			$data = $this->getAllParams(true);
			if (isset($data['format'])) {
				unset($data['format']);
			}
		} else {
			$miss_params = array();
			foreach ($params as $param) {
				if(!$this->hasParam($param)) {
					$miss_params[] = $param;
				} else {
					$data[$param] = $this->getParam($param, '');
				}
			}

			if(!empty($miss_params)) {
				$this->_request->setError(200, 'Missing pramas: ' . implode(',', $miss_params));
				return;
			}

		}

		return $data;
	}

	public function getPassword($pass = NULL) {
		return md5(sha1(SECURITYKEY.$pass));
	}

	public function checkSame($a, $b) {
		return strcmp($a, $b)== 0 ? true : false;
	}

	protected function getRandom(){
		return substr(str_shuffle(str_repeat('abcdefghijklmnopqrstuvwxyzABCDEFGHJKLMNOP1234567890',5)),0,8);
	}

	public function isJson($string) {
		json_decode($string);
		if(json_last_error() == JSON_ERROR_NONE) {
			return true;
		} else {
			$this->_request->setError(200, 'Invalid json');
			return;
		}
// 		return (json_last_error() == JSON_ERROR_NONE);
	}
}
