<?php
/*
 * @category   Base
 */
class Result {
	public $data;

	public function __construct($data_sp){
		$this->data = $data_sp;
	}

	public function first() {
		if(isset($this->data[0])) {
			return $this->data[0];
		}

		return array();
	}

	public function firstRow() {
		if(isset($this->data[0][0])) {
			return $this->data[0][0];
		}

		return array();
	}

	public function hasField($field = '') {
		if(!isset($this->data[0][0][$field])
				|| is_null($this->data[0][0][$field])
				|| $this->data[0][0][$field] == ''
		) {
			return false;
		}

		return true;
	}

	public function getStatus() {
		$rp = new Respon();
		$rp->Status = NG;
		if(isset($this->data[0][0]['status']) && $this->data[0][0]['status'] == '200') {
			$rp->Status = OK;
		}

		return $rp;
	}
}