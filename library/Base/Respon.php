<?php
/*
 * @category   Base
 */
class Respon {
	public $Status;
	public $Data;
	public $Message;
	public $Exception;

	public function __construct(){
		$this->Status = OK;
	}
}